<?php


namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class PdfGeneratorService {

    private Environment $twig;
    private KernelInterface $kernel;

    public function __construct(Environment $twig, KernelInterface $kernelInterface) {
        $this->twig = $twig;
        $this->kernel = $kernelInterface;
    }

public function generatePdf($data, $fileName, $template, $destinationPath): string
{
    // 1 - Mettre en place les options du PDF
    $pdfOptions = new Options();
    $pdfOptions->set(['defaultFont' => 'Arial', 'enable_remote' => true]);

    // 2 - Créer le PDF
    $domPdf = new Dompdf($pdfOptions);

    // 3 - Préparer le template
    $html = $this->twig->render($template, $data);

    $domPdf->loadHtml($html);
    $domPdf->setPaper('A4', 'portrait');
    $domPdf->render();

    $invoicePDF = $domPdf->output();

    // 4 - Déterminer le chemin d’enregistrement (public/...)
    $uploadDirectory = rtrim($this->kernel->getProjectDir(), '/') . '/public/' . trim($destinationPath, '/') . '/';

    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $fullPath = $uploadDirectory . $fileName;
    if (!is_writable($uploadDirectory)) {
    throw new \RuntimeException("Le dossier n'est pas accessible en écriture : $uploadDirectory");
}

    file_put_contents($fullPath, $invoicePDF);

    // 5 - Retourner le chemin complet du fichier enregistré
    return $fullPath;
}


}


?>