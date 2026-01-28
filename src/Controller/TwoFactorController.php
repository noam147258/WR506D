<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TwoFactorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/2fa')]
#[IsGranted('ROLE_USER')]
class TwoFactorController extends AbstractController
{
    public function __construct(
        private readonly TwoFactorService $twoFactorService,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/setup', name: 'app_2fa_setup', methods: ['POST'])]
    public function setup(): JsonResponse
    {
        try {
            $user = $this->getUser();

            if (!$user instanceof User) {
                return $this->json(['error' => 'User not found'], 401);
            }

            $secret = $this->twoFactorService->generateSecret();
            $user->setTwoFactorSecret($secret);
            // ON FORCE A FALSE !
            $user->setTwoFactorEnabled(false);

            $this->entityManager->flush();

            // Recharger l'utilisateur depuis la base pour avoir le secret à jour
            $this->entityManager->refresh($user);

            // QR code
            $qrCodeDataUri = $this->twoFactorService->getQrCode($user);
            //--- URL de provision générée par le module OTP
            $provisioningUri = $this->twoFactorService->getProvisioningUri($user);

            return $this->json([
                'secret' => $secret,
                'qr_code' => $qrCodeDataUri,
                'provisioning_uri' => $provisioningUri,
                'message' => 'Scannez le QR code avec votre application d\'authentification (Google Authenticator, Authy, etc.)',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to setup 2FA',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    #[Route('/enable', name: 'app_2fa_enable', methods: ['POST'])]
    public function enable(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'User not found'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? '';

        if (empty($code)) {
            return $this->json(['error' => 'Code is required'], 400);
        }

        // Verify the code
        if (!$this->twoFactorService->verifyCode($user, $code)) {
            return $this->json(['error' => 'Invalid code'], 400);
        }

        // Generate backup codes
        $backupCodes = $this->twoFactorService->generateBackupCodes();
        $hashedBackupCodes = $this->twoFactorService->hashBackupCodes($backupCodes);

        // Enable 2FA
        $user->setTwoFactorEnabled(true);
        $user->setTwoFactorBackupCodes($hashedBackupCodes);

        $this->entityManager->flush();

        return $this->json([
            'message' => '2FA enabled successfully',
            'backup_codes' => $backupCodes,
            'warning' => 'Save these backup codes in a safe place. You will need them if you lose access to your authenticator app.',
        ]);
    }
}
