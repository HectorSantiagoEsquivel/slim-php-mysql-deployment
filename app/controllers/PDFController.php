<?php

require_once './utils/Validador.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFController 
{

    public function DescargarPDF($request, $response, $args)
    {

        $rutaImagen = __DIR__ . '/../resources/Comanda_Imagen.png';
    
    
        $tipo = pathinfo($rutaImagen, PATHINFO_EXTENSION);
        $data = file_get_contents($rutaImagen);
        $base64 = 'data:image/' . $tipo . ';base64,' . base64_encode($data);

        $html = "<html>
                    <body>
                        <h1></h1>
                        <img src='{$base64}' alt='Mi Imagen' style='width:100%; height:auto;'/>
                    </body>
                 </html>";
                 

        $optionsDompdf = new Options();
        $optionsDompdf->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($optionsDompdf);
    
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        
        $response->getBody()->write($dompdf->output());
        return $response->withHeader('Content-Type', 'application/pdf')
                        ->withHeader('Content-Disposition', 'attachment; filename="Comanda.pdf"');
    }
    
    
}
