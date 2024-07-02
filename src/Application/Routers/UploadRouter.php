<?php

namespace App\Application\Routers;

use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\BancoPreguntas;
use App\Application\Models\Pregunta;
use App\Application\Models\Respuesta;
use App\Application\Models\Trabajador;
use App\Application\Models\Usuario;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use Slim\App;
use Slim\Psr7\Stream;
use TCPDF;

/**
 * Class UploadRouter.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Routers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class UploadRouter implements HttpStatusCodes
{
    /**
     * Constructor for the class.
     *
     * @param App $app The application instance.
     */
    public function __construct(App $app)
    {
        $app->get('/images/thumbnails/{name}', function (Request $request, Response $response, array $args) {
            $directory = $this->get('directory_thumbnails');
            $file = $directory . DIRECTORY_SEPARATOR . $args['name'];
            if (!file_exists($file)) {
                //die("file:$file");
                $response->getBody()->write($file);
            }
            $image = file_get_contents($file);
            if ($image === false) {
                $response->getBody()->write("error getting image");
            }
            $response->getBody()->write($image);
            return $response->withHeader('Content-Type', 'image/png');
        });

        $app->post('/images/thumbnails', function (Request $request, Response $response) {
            $directory = $this->get('directory_thumbnails');
            $uploadedFiles = $request->getUploadedFiles();

            // handle single input with single file upload
            $uploadedFile = $uploadedFiles['image'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = self::moveUploadedFile($directory, $uploadedFile);
            }
        
            $res = MessageResponse::getInstance(self::HTTP_OK, self::HTTP_OK_MESSAGE, [['filename' => $filename]]); 
            $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

            return $response->withStatus(self::HTTP_OK);
            //return $response;
        });

        //function for signature upload
        $app->get('/signature/{name}', function (Request $request, Response $response, array $args) {
            $directory = $this->get('directory_signature');
            $file = $directory . DIRECTORY_SEPARATOR . $args['name'];
            if (!file_exists($file)) {
                $response->getBody()->write($file);
            }
            $image = file_get_contents($file);
            if ($image === false) {
                $response->getBody()->write("error getting image");
            }
            $response->getBody()->write($image);
            return $response->withHeader('Content-Type', 'image/png');
        });

        $app->post('/signature', function (Request $request, Response $response) {
            $directory = $this->get('directory_signature');
            $uploadedFiles = $request->getUploadedFiles();

            $uploadedFile = $uploadedFiles['signature'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = self::moveUploadedFile($directory, $uploadedFile);
            }
            $res = MessageResponse::getInstance(self::HTTP_OK, self::HTTP_OK_MESSAGE, [['filename' => $filename]]); 
            $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

            return $response->withStatus(self::HTTP_OK);
        });

        $app->get('/images/logos/{name}', function (Request $request, Response $response, array $args) {
            $directory = $this->get('directory_logos');
            $file = $directory . DIRECTORY_SEPARATOR . $args['name'];
            if (!file_exists($file)) {
                $response->getBody()->write($file);
            }
            $image = file_get_contents($file);
            if ($image === false) {
                $response->getBody()->write("error getting image");
            }
            $response->getBody()->write($image);
            return $response->withHeader('Content-Type', 'image/png');
        });

        $app->post('/images/logos', function (Request $request, Response $response) {
            $directory = $this->get('directory_logos');
            $uploadedFiles = $request->getUploadedFiles();

            $uploadedFile = $uploadedFiles['logo'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = self::moveUploadedFile($directory, $uploadedFile);
            }

            $res = MessageResponse::getInstance(self::HTTP_OK, self::HTTP_OK_MESSAGE, [['filename' => $filename]]); 
            $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

            return $response->withStatus(self::HTTP_OK);
        });


        $app->get('/videos/clases/{name}', function (Request $request, Response $response, array $args) {
            $directory = $this->get('directory_videos');
            $file = $directory . DIRECTORY_SEPARATOR . $args['name'];
            
            $fh = fopen($file, 'rb');
            $file_stream = new Stream($fh);

            return $response->withBody($file_stream)
                ->withHeader('Content-Disposition', 'attachment; filename='.$args['name'].';')
                ->withHeader('Content-Type', mime_content_type($file))
                ->withHeader('Content-Length', filesize($file));
        });

        $app->post('/videos/clases', function (Request $request, Response $response) {
            try {
                $directory = $this->get('directory_videos');
                $uploadedFiles = $request->getUploadedFiles();
                $max_size = ini_get('upload_max_filesize');
                $uploadedFile = $uploadedFiles['video'];
                $file_size = $uploadedFile->getSize();


                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    $filename = self::moveUploadedFile($directory, $uploadedFile);
                }
                else {
                    $res = MessageResponse::getInstance(self::HTTP_BAD_REQUEST, self::HTTP_BAD_REQUEST_MESSAGE, [['error' => $uploadedFile->getError(), 'upload_max_filesize' => $max_size, "file_size" => $file_size]]); 
                    $response->withHeader('Content-Type', 'application/json')
                        ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
                    return $response->withStatus(self::HTTP_BAD_REQUEST);
                }

                $res = MessageResponse::getInstance(self::HTTP_OK, self::HTTP_OK_MESSAGE, [['filename' => $filename]]); 
                $response->withHeader('Content-Type', 'application/json')
                    ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

                return $response->withStatus(self::HTTP_OK);
            } catch (Exception $e) {
                $res = MessageResponse::getInstance(self::HTTP_BAD_REQUEST, self::HTTP_BAD_REQUEST_MESSAGE, [['error' => $e->getMessage(), 'upload_max_filesize' => $max_size, "file_size" => $file_size]]);
                $response->withHeader('Content-Type', 'application/json')
                    ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
                return $response->withStatus(self::HTTP_BAD_REQUEST);
            }
        });

        $app->get('/files/clases/{name}', function (Request $request, Response $response, array $args) {
            $directory = $this->get('directory_files');
            $file = $directory . DIRECTORY_SEPARATOR . $args['name'];
            
            $fh = fopen($file, 'rb');
            $file_stream = new Stream($fh);

            return $response->withBody($file_stream)
                ->withHeader('Content-Disposition', 'attachment; filename='.$args['name'].';')
                ->withHeader('Content-Type', mime_content_type($file))
                ->withHeader('Content-Length', filesize($file));
        });

        $app->post('/files/clases', function (Request $request, Response $response) {
            $directory = $this->get('directory_files');
            $uploadedFiles = $request->getUploadedFiles();

            $uploadedFile = $uploadedFiles['file'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = self::moveUploadedFile($directory, $uploadedFile);
            }
            $res = MessageResponse::getInstance(self::HTTP_OK, self::HTTP_OK_MESSAGE, [['filename' => $filename]]); 
            $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

            return $response->withStatus(self::HTTP_OK);
        });

        // Import Excel banco-preguntas to database
        $app->post('/excel/banco-preguntas', function (Request $request, Response $response) {
            $status = self::HTTP_OK;
            $uploadedFiles = $request->getUploadedFiles();
            // Get all POST parameters
            $params = (array)$request->getParsedBody();

            $uploadedFile = $uploadedFiles['file'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['file']['tmp_name']);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();

                //remove header
                array_shift($sheetData);

                foreach ($sheetData as $row) {
                    $pregunta = new Pregunta([
                        'id_banco_preguntas' => $params['id_banco_preguntas'],
                        'pregunta' => $row[0],
                        'nota' => 2
                    ]);
                    $pregunta->save();
                    $respuestaCorrecta = $row[1];
                    $pregunta->refresh()->respuestas()->saveMany([
                        new Respuesta([
                            'id_pregunta' => $pregunta->id,
                            'respuesta' => $row[2],
                            'correcta' => $respuestaCorrecta == "1" ? 1 : 0,
                        ]),
                        new Respuesta([
                            'id_pregunta' => $pregunta->id,
                            'respuesta' => $row[3],
                            'correcta' => $respuestaCorrecta == "2" ? 1 : 0,
                        ]),
                        new Respuesta([
                            'id_pregunta' => $pregunta->id,
                            'respuesta' => $row[4],
                            'correcta' => $respuestaCorrecta == "3" ? 1 : 0,
                        ]),
                        new Respuesta([
                            'id_pregunta' => $pregunta->id,
                            'respuesta' => $row[5],
                            'correcta' => $respuestaCorrecta == "4" ? 1 : 0,
                        ])
                    ]);
                }

                $bancoPreguntas = BancoPreguntas::find($params['id_banco_preguntas']);
                $bancoPreguntas['empresaCliente'] = $bancoPreguntas->clase->periodo->empresaCliente;
                $bancoPreguntas['periodo'] = $bancoPreguntas->clase->periodo;
                $bancoPreguntas['clase'] = $bancoPreguntas->clase;
                $bancoPreguntas->load(['preguntas','preguntas.respuestas']);
            
                $res = MessageResponse::getInstance(self::HTTP_OK, self::HTTP_OK_MESSAGE, [$bancoPreguntas]); 
            }
            else {
                $status = self::HTTP_BAD_REQUEST;
                $res = MessageResponse::getInstance(self::HTTP_BAD_REQUEST, self::HTTP_BAD_REQUEST_MESSAGE, [['error' => $uploadedFile->getError()]]);
            }

            $response->withHeader('Content-Type', 'application/json')
                ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

            return $response->withStatus($status);
        });

        // Import Excel trabajadores to database
        $app->post('/excel/trabajadores', function (Request $request, Response $response) {
            $status = self::HTTP_OK;
            $uploadedFiles = $request->getUploadedFiles();
            // Get all POST parameters
            $params = (array)$request->getParsedBody();

            $uploadedFile = $uploadedFiles['file'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($_FILES['file']['tmp_name']);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();

                //remove header
                array_shift($sheetData);

                foreach ($sheetData as $row) {

                    if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[4]) || empty($row[5]) || empty($row[6])) {
                        continue;
                    }

                    $trabajador = new Trabajador([
                        'id_empresa_cliente' => $params['id_empresa_cliente'],
                        'nombres' => $row[0],
                        'apellidos' => $row[1],
                        'dni' => $row[2],
                        'area' => $row[3],
                        'puesto' => $row[4],
                        'sede' => $row[5],
                        'fecha_nacimiento' => $row[6],
                    ]);
                    $trabajador->save();

                    $usuario = new Usuario([
                        'id_trabajador' => $trabajador->id,
                        'nombres' => $row[0],
                        'apellidos' => $row[1],
                        'usuario' => $row[2],
                        'clave' => password_hash($row[2], PASSWORD_DEFAULT),
                        'tipo' => 'trabajador',
                        'activo' => 1
                    ]);
                    $usuario->save();
                }

                $trabajadores = Trabajador::where('id_empresa_cliente', $params['id_empresa_cliente'])->get();
                $res = MessageResponse::getInstance(self::HTTP_OK, self::HTTP_OK_MESSAGE, $trabajadores); 
            }
            else {
                $status = self::HTTP_BAD_REQUEST;
                $res = MessageResponse::getInstance($status, self::HTTP_BAD_REQUEST_MESSAGE, [['error' => $uploadedFile->getError()]]);
            }
            
            $response->withHeader('Content-Type', 'application/json')
                ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

            return $response->withStatus($status);
        });

        $app->get('/test/demo', function (Request $request, Response $response, $args) {

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(2);
            $pdf->AddPage('P',"A4");
            
            $svgString = urldecode(substr($request->getParsedBody()['id'], strlen('data:image/svg+xml;charset=UTF-8,')));
            $pdf->ImageSVG('@' . $svgString, $x=15, $y=30, $w='', $h='', $link='', $align='', $palign='', $border=0, $fitonpage=false);
            $pdf->Write(0, $txt='', '', 0, 'L', true, 0, false, false, 0);
            
            //Close and output PDF document
            $pdf->Output(dirname(__DIR__).'/../../uploads/certificados/' . 'example_002.pdf', 'F');

            $response->getBody()->write($request->getParsedBody()['id']);
            return $response->withStatus(self::HTTP_OK);
        });
    }

    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param string $directory The directory to which the file is moved
     * @param UploadedFileInterface $uploadedFile The file uploaded file to move
     *
     * @return string The filename of moved file
     */
    public static function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    
        // see http://php.net/manual/en/function.random-bytes.php
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
    
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    
        return $filename;
    }
}
