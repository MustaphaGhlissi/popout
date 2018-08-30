<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 05/03/2018
 * Time: 16:37
 */

namespace App\Service;


use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
class QRCodeGenerator
{
    public function generateQRCode(User $user, ContainerInterface $container)
    {
        $qrCode = new Png();
        $qrCode->setWidth(256)
                ->setHeight(256);
        $qrCodeAbsolutePath = $container->getParameter('qrcode_dir').'/uc'.$user->getId().'-qrcode.png';
        $qrCodeRelativePath = 'uploads/qrcodes/uc'.$user->getId().'-qrcode.png';
        $writer = new Writer($qrCode);
        $writer->writeFile($user->getId(), $qrCodeAbsolutePath);
        $user->setQrCode($qrCodeRelativePath);
    }
}