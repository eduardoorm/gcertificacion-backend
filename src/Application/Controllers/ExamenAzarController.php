<?php

namespace App\Application\Controllers;

use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\BancoPreguntas;
use App\Application\Models\Clase;
use App\Application\Models\ClaseTrabajador;
use App\Application\Models\ExamenAzar;
use App\Application\Models\ExamenAzarPregunta;
use App\Application\Models\Pregunta;
use App\Application\Models\Respuesta;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use setasign\Fpdi\Tcpdf\Fpdi;

date_default_timezone_set('America/Lima');

/**
 * Class ExamenAzarController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ExamenAzarController implements HttpStatusCodes
{
    /**
     * Retrieves all the clases and returns a JSON-encoded response.
     *
     * @param ResponseInterface $response response object to be returned
     * @throws None
     * @return ResponseInterface response object containing JSON-encoded clases
     */
    public function index(ResponseInterface $response)
    {
        $examenes = ExamenAzar::all();
        $examenes = ['examenes_azar' => $examenes];
        $response->getBody()->write(json_encode($examenes, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieves a clase by ID and returns it as a JSON response.
     *
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface HTTP response object with JSON data and headers
     */
    public function find(Request $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $clase = ExamenAzar::find($id);
        $response->getBody()->write(json_encode($clase, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieve a examen by ID of clase-trabajador and returns it as a JSON response.
     * 
     * @param Request $request HTTP request object
     * @param ResponseInterface $response HTTP response object
     * @param array $args Route parameters
     * @throws Some_Exception_Class if examen is not found
     * @return ResponseInterface HTTP response object with JSON data and headers
     * */
    public function byClaseTrabajador(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id_clase_trabajador = $args['id'];
        try {
            $examen = ExamenAzar::where('id_clase_trabajador', $id_clase_trabajador)->orderBy('numero_intento', 'DESC')->first();
            $id_clase = ClaseTrabajador::find($id_clase_trabajador)->first()->id_clase;
            $clase = Clase::find($id_clase)->first();
            if($examen) $examen = $examen->toArray();
            $examen['tipo'] = $clase->tipo;

            if (!$examen) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
            }
            else {
                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$examen]);
            }
        } catch (QueryException $e) {
            $status = self::HTTP_NOT_FOUND;
            $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response;
    }

    /**
     * Creates a new Clase object from the parsed body of the HTTP request and writes the 
     * JSON-encoded object to the response body. Sets the response content type to JSON.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class If there was an error creating the Clase object
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function store(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_CREATED;
        $id_clase_trabajador = $request->getParsedBody()['id_clase_trabajador'];
        
        try {
            $ct = ClaseTrabajador::where('id', $id_clase_trabajador)->first();
            $maximo = $ct->numero_intentos;
            $id_clase = $ct->id_clase;
            //$count = ExamenAzar::where('id_clase_trabajador', $id_clase_trabajador)->count();
            
            $examenes_azar = ExamenAzar::where('id_clase_trabajador', $id_clase_trabajador)->orderBy('id', 'DESC')->get();
            $ultimo_examen = $examenes_azar->first();
            $count = $examenes_azar->count();

            if (!BancoPreguntas::where('id_clase', $id_clase)->first()) {
                $status = self::HTTP_NOT_FOUND;
                $res = MessageResponse::getInstance($status, "No existe un banco de preguntas para la clase", []);
            }
            else if($ultimo_examen && intval($ultimo_examen->aprobado) === -1){
                    $examen = clone $ultimo_examen;
                    $preguntas_ = $ultimo_examen->load('preguntas');
                    $preguntas = array();
                    foreach ($preguntas_->preguntas as $pregunta_) {
                        $preguntas[] =  Pregunta::find($pregunta_->id_pregunta)->load('respuestas');
                        $respuestas = $preguntas[count($preguntas) - 1]['respuestas']->toArray();
                        for($i=0; $i<4; $i++){
                            unset($respuestas[$i]['correcta']);
                        }
                        unset($preguntas[count($preguntas) - 1]['respuestas']);
                        $preguntas[count($preguntas) - 1]['respuestas'] = $respuestas;
                    };
                    $examen['preguntas'] = $preguntas;
                    $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$examen]);
                
            }
            else if($count >= $maximo){
                $status = self::HTTP_BAD_REQUEST;
                $res = MessageResponse::getInstance($status, "Ya superó la cantidad de intentos para rendir el examen", []);
            }
            else {
                //Obtener el total de preguntas del banco de preguntas
                $banco = BancoPreguntas::where('id_clase', $id_clase)->first()->loadCount('preguntas');
                $banco->preguntas;

                $numbers = array();
                
                while (count($numbers) < 10) {
                    $randomNumber = rand(1, 30);

                    if (!in_array($randomNumber, $numbers)) {
                        $numbers[] = $randomNumber;
                    }
                }

                $preguntas = array();
                
                foreach ($numbers as $number) {
                    $preguntas[] = $banco->preguntas[$number - 1]->load('respuestas');
                    $respuestas = $preguntas[count($preguntas) - 1]['respuestas']->toArray();
                    
                    for($i=0; $i<4; $i++){
                        unset($respuestas[$i]['correcta']);
                    }
                    
                    unset($preguntas[count($preguntas) - 1]['respuestas']);
                    $preguntas[count($preguntas) - 1]['respuestas'] = $respuestas;
                }

                $examen = ExamenAzar::create([
                    'id_clase_trabajador' => $id_clase_trabajador, 
                    'numero_intento' => $count + 1,
                    'fecha' => date('Y-m-d H:i:s'), 
                    'aprobado' => -1,
                ]);

                foreach ($preguntas as $pregunta) {
                    $eap = ExamenAzarPregunta::create(['id_examen_azar' => $examen->id, 'id_pregunta' => $pregunta['id']]);
                }

                $examen['preguntas'] = $preguntas;

                $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$examen]);
            }

            //$examen = ExamenAzar::create($request->getParsedBody());
            //$res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$examen]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al crear la clase: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    
    /**
     * Solves the given exam by updating the database with the chosen answers
     * and calculating the result.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class description of exception
     * @return ResponseInterface The updated HTTP response object
     */
    public function solve(Request $request, ResponseInterface $response)
    {
        $status = self::HTTP_OK;
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $examen_resuelto = $request->getParsedBody();
        $id_examen_azar = $examen_resuelto['id_examen_azar'];
        $respuestas_elegidas = $examen_resuelto['respuestas_elegidas'];
        $respuestas_correctas = 0;
        $respuestas_incorrectas = 0;
        $nota = 0;
        $examen_azar = ExamenAzar::find($id_examen_azar);
        $clase = $examen_azar->clase_trabajador->clase;

        if(!$examen_azar){
            $status = self::HTTP_NOT_FOUND;
            $res = MessageResponse::getInstance($status, self::HTTP_NOT_FOUND_MESSAGE, []);
        }
        else {
            $examen_azar_preguntas = ExamenAzarPregunta::where('id_examen_azar', $id_examen_azar)->get();

            //Actualizar ExamenAzarPregunta con la respuesta elegida durante el examen y verifica si la respuesta es correcta
            foreach ($examen_azar_preguntas as $examen_azar_pregunta) {
                foreach ($respuestas_elegidas as $respuesta_elegida) {
                    if ($examen_azar_pregunta->id_pregunta == $respuesta_elegida['id_pregunta']) {
                        $examen_azar_pregunta->update(['id_respuesta' => $respuesta_elegida['id_respuesta']]);

                        $pregunta = Pregunta::find($respuesta_elegida['id_pregunta']);
                        $respuesta = Respuesta::find($respuesta_elegida['id_respuesta']);
                        if(!$respuesta){
                            $respuestas_incorrectas++;
                        }
                        else if(intval($respuesta->correcta) === 1){
                            $respuestas_correctas++;
                            $nota += $pregunta->nota;
                        }
                        else{
                            $respuestas_incorrectas++;
                        }

                        break;
                    }
                }
            }

            $trabajador = $examen_azar->clase_trabajador->trabajador;

            /*
            * El certificado se genera a partir de una plantilla no óptima, a pesar de las indicaciones, el usuario a insistido
            * en mantener la plantilla:
            * - La plantilla contiene comillas para el nombre del curso. Las comillas las debería escribir el sistema para que vaya de acuerdo con el largo del texto
            * - La plantilla tiene escrita parte de la fecha. Esto provoca que el sistema no coloque la fecha adecuamente en todos los escenarios.
            */
            //Generar certificado en pdf
            $plantillaPDF = dirname(__DIR__) . '/../../templates/certificates/capacitacion.pdf';
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($plantillaPDF);
            $templateId = $pdf->importPage(1);
            $pdf->AddPage('LANDSCAPE');
            $pdf->useTemplate($templateId);

            $pdf->setFont('dejavusans', '', 13);
            $pdf->SetTextColor(64, 44, 112);
            $pdf->setXY(90, 78);
            $pdf->Cell(0, 0, 'Otorgado a:', 0, 1, 'C');
            
            $pdf->SetFont('pinyonscript', '', 46);
            $pdf->SetTextColor(212, 175, 55);
            $pdf->SetXY(80, 90);
            $pdf->Cell(0, 10, $trabajador->nombres . ' ' . $trabajador->apellidos, 0, 1, 'C', false, null, 1);

            $pdf->setFont('dejavusans', '', 13);
            $pdf->SetTextColor(64, 44, 112);
            $pdf->SetXY(90, 116);
            $pdf->Cell(0, 0, 'Por haber participado y aprobado satisfactoriamente la', 0, 1, 'C');
            
            $pdf->setFont('dejavusans', '', 13);
            $pdf->SetTextColor(64, 44, 112);
            $pdf->SetXY(90, 122);
            $pdf->Cell(0, 0, 'capacitación de:', 0, 1, 'C');
            
            $pdf->setFont('dejavusans', 'B', 13);
            $pdf->SetTextColor(64, 44, 112);
            $pdf->SetXY(90, 128);
            $pdf->Cell(0, 0, mb_strtoupper($clase->titulo, 'UTF-8'), 0, 1, 'C');
            
            $pdf->setFont('dejavusans', '', 13);
            $pdf->SetTextColor(64, 44, 112);
            $pdf->SetXY(90, 134);
            $pdf->Cell(0, 0, 'Organizado por Global Certificación S.A.C.', 0, 1, 'C');
            
            $pdf->SetFont('dejavusans', 'B', 13);
            $pdf->SetXY(90, 145);
            $pdf->Cell(0, 0, date('d'). ' de '. $meses[date('n') - 1] . ' de ' . date('Y'), 0, 1, 'C');

            $filename = $trabajador->dni . '-' . $examen_azar->clase_trabajador->clase->id;
            $pdf->output(dirname(__DIR__).'/../../uploads/certificados/' . $filename . '.pdf', 'F');

            //Actualizar ExamenAzar con el resultado del examen
            $examen_azar->update([
                'aprobado' => $nota >= 12 ? 1 : 0, 
                'respuestas_correctas' => $respuestas_correctas, 
                'respuestas_incorrectas' => $respuestas_incorrectas,
                'nota' => $nota,
                'certificado' => 'http://backlab.gcertificacion.pe/gcertificacion/api/certificados/' . $filename . '.pdf',
            ]);

            $examen_azar = $examen_azar->toArray();
            $examen_azar['tipo'] = $clase->tipo;
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$examen_azar]);
        }
        
        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    public function pdf(Request $request, ResponseInterface $response, array $args)
    {
        $plantillaPDF = 'templates/certificates/template.pdf';
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($plantillaPDF);
        $templateId = $pdf->importPage(1);
        $pdf->AddPage('LANDSCAPE');
        $pdf->useTemplate($templateId);

        $pdf->SetFont('times', 'B', 40);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(0, 128);
        $pdf->Cell(0, 10, 'Adolfo Julcamoro Quiroz', 0, 1, 'C');

        $content = $pdf->output('doc.pdf', 'I');

        $response->getBody()->write($content);

        $response = $response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="filename.pdf"');

        return $response;
    }

    /**
     * Updates a clase with the given ID.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function update(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        $examen = ExamenAzar::find($id);
        $examen->update($request->getParsedBody());
        $response->getBody()->write(json_encode($examen, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Deletes a clase with the given ID.
     *
     * @param Request $request The HTTP request object
     * @param ResponseInterface $response The HTTP response object
     * @throws Some_Exception_Class if clase is not found
     * @return ResponseInterface The HTTP response object with the JSON-encoded Clase object in its body and a Content-Type header of 'application/json'
     */
    public function delete(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];
        $clase = ExamenAzar::find($id);
        $clase->delete($request->getParsedBody());
        $response->getBody()->write(json_encode($clase, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-Type', 'application/json');
    }
}