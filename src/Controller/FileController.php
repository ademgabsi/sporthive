<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class FileController extends AbstractController
{
    #[Route('/uploads/{type}/{filename}', name: 'app_file_serve')]
    public function serveFile(string $type, string $filename): BinaryFileResponse
    {
        $directory = $this->getParameter($type . '_directory');
        $filePath = $directory . '/' . $filename;
        
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found');
        }
        
        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
        
        return $response;
    }
} 