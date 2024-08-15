<?php

namespace App\Application\Controllers;

use App\Application\Factory\HttpStatusCodes;
use App\Application\Factory\MessageResponse;
use App\Application\Models\Archivo;
use App\Application\Models\ArchivoTrabajador;
use App\Application\Models\Clase;
use App\Application\Models\ClaseTrabajador;
use App\Application\Models\EmpresaCliente;
use App\Application\Models\ExamenAzar;
use App\Application\Models\Trabajador;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use setasign\Fpdi\Tcpdf\Fpdi;
use Slim\Psr7\Stream;
use TCPDF_FONTS;

/**
 * Class ReporteController.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Controllers
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class ReporteController implements HttpStatusCodes
{    
    
    public function __construct()
    {
        date_default_timezone_set('America/Lima');
    }

    public function induccion(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];

        /*
        inducción:
        - Video
        - Documentos
        - Examen
        - Certificado
        */ 

        try {            
            $data = $this->induccionDataReport($id);
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$data]);
        }
        catch(QueryException $e){
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la clase: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    public function capacitacion(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];

        /*
        capacitación:
        - Video
        - Documentos
        - Examen
        - Certificado
        */ 

        try {
            $data = $this->capacitacionDataReport($id);
            unset($data['rows']);
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$data]);
        }
        catch(QueryException $e){
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la clase: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    public function documentacion(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $id = $args['id'];

        try {
            $data = $this->documentacionDataReport($id);
            $res = MessageResponse::getInstance($status, self::HTTP_OK_MESSAGE, [$data]);
        } catch (QueryException $e) {
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la clase: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);

    }

    public function induccionDataReport($idEmpresa): array
    {
        /*
        inducción:
        - Video
        - Documentos
        - Examen
        - Certificado
        */ 

        try {
            $empresa = EmpresaCliente::find($idEmpresa);
            $periodo = $empresa->periodos()->where('activo', 1)->first();
            $clasesInduccion = $periodo->clases()->where('tipo', 'induccion')->get();
            $totalInduccion = 0;
            $avanceInduccion = 0;
            $avanceAreas = [];
            $avanceAreas_ = [];
            
            foreach ($clasesInduccion as $clase) {
                $totalInduccion += $clase->trabajadores->count();
                foreach($clase->trabajadores as $trabajador){
                    $examenAprobado = ExamenAzar::where('id_clase_trabajador', $trabajador->clases_trabajadores->id)
                        ->where('aprobado', 1)
                        ->count();
                    $avanceInduccion += $examenAprobado ? 1 : 0;
                    
                    if(isset($avanceAreas[$trabajador->area])){
                        if($examenAprobado){
                            $avanceAreas[$trabajador->area][0] = $avanceAreas[$trabajador->area][0] + 1; 
                            $avanceAreas[$trabajador->area][1] = $avanceAreas[$trabajador->area][1] + 1;
                        } 
                    }
                    else{
                        $avanceAreas[$trabajador->area] = $examenAprobado ? [1, 1] : [0, 1]; //[aprobados, matriculados]
                    }
                }
            }

            //Recorrer el array $avanceAreas y calcular el porcentaje por cada area
            foreach ($avanceAreas as $key => $value) {
                $avanceAreas[$key] = round(($value[0] / $value[1]) * 100, 2);
                $avanceAreas_[] = ['name' => $key, 'value' => $avanceAreas[$key]];
            }

            $data = [
                'avanceInduccion' => [ //Porcentaje de avance total
                    ['name' => 'Finalizado', 'value' => $avanceInduccion],
                    ['name' => 'Pendiente', 'value' => $totalInduccion - $avanceInduccion],
                ], 
                'avanceAreasInduccion' => $avanceAreas_ //Porcentaje de avance por area
            ];
        }
        catch(QueryException $e){
            throw $e;
        }

        return $data;
    }

    public function capacitacionDataReport($idClase)
    {
        /*
        capacitación:
        - Video
        - Documentos
        - Examen
        - Certificado
        */ 

        try {
            $clasesCapacitacion = Clase::where('id', $idClase)->get();
            $totalCapacitacion = 0;
            $avanceCapacitacion = 0;
            $avanceAreas = [];
            $avanceAreas_ = [];
            $rows = [];
            
            foreach ($clasesCapacitacion as $clase) {
                $totalCapacitacion += $clase->trabajadores->count();
                foreach($clase->trabajadores as $trabajador){
                    $examenes = ExamenAzar::where('id_clase_trabajador', $trabajador->clases_trabajadores->id)->get();
                    $avanceCapacitacion += $examenes->where('aprobado', 1)->count() > 0 ? 1 : 0;
                    
                    if(isset($avanceAreas[$trabajador->area])){
                        if($examenes) {
                            $avanceAreas[$trabajador->area][0] = $avanceAreas[$trabajador->area][0] + 1; 
                            $avanceAreas[$trabajador->area][1] = $avanceAreas[$trabajador->area][1] + 1;
                        }
                    }
                    else {
                        $avanceAreas[$trabajador->area] = $examenes->where('aprobado', 1)->count() > 0 ? [1, 1] : [0, 1]; //[aprobados, matriculados]
                    }

                    //listado para el informe
                    $trabajador_ = array_replace([], $trabajador->toArray());
                    unset($trabajador_['clases_trabajadores']);
                    $clase_ = array_replace([], $clase->toArray());
                    unset($clase_['trabajadores']);
                    $examen = $examenes->isEmpty() ? Collection::empty() : $examenes->toQuery()->orderBy('fecha', 'desc')->first();
                    $examen_ = array_replace([], $examen->toArray());
                    $rows[] = array_merge($trabajador_, $clase_, $examen_);
                }
            }

            //Recorrer el array $avanceAreas y calcular el porcentaje por cada area
            foreach ($avanceAreas as $key => $value) {
                $avanceAreas[$key] = round(($value[0] / $value[1]) * 100, 2);
                $avanceAreas_[] = ['name' => $key, 'value' => $avanceAreas[$key]];
            }

            $data = [
                'avanceCapacitacion' => [ //Porcentaje de avance total
                    ['name' => 'Finalizado', 'value' => $avanceCapacitacion],
                    ['name' => 'Pendiente', 'value' => $totalCapacitacion - $avanceCapacitacion],
                ], 
                'avanceAreasCapacitacion' => $avanceAreas_, //Porcentaje de avance por area
            ];

        }
        catch(QueryException $e){
            throw $e;
        }

        return $data;
    }

    public function documentacionDataReport($idArchivo)
    {
        $totalArchivos = 0;
        $totalAceptados = 0;
        $trabajadoresArea = [];
        $totalArea = [];
        $avanceAreasDescarga_ = [];
        $avanceAreasDeclaracionJurada = [];
        $avanceAreasDeclaracionJurada_ = [];

        try {
            $archivo = Archivo::find($idArchivo);
            $clase = $archivo->clase;
            
            $clasesTrabajadores = ClaseTrabajador::where('id_clase', $clase->id)->get();
            $clasesTrabajadores->load(['trabajador.archivos']);
        

            foreach($clasesTrabajadores as $claseTrabajador){

                //Calcula el total global de declaraciones juradas que deben existir
                $totalArchivos++;

                //Calcula el total global de declaraciones juradas aceptadas
                $totalAceptados += $claseTrabajador->trabajador->archivos->contains($idArchivo) ? 1 : 0;
                
                //Calcula el total de declaraciones juradas aceptadas por area
                if(isset($avanceAreasDeclaracionJurada[$claseTrabajador->trabajador->area])){
                    $avanceAreasDeclaracionJurada[$claseTrabajador->trabajador->area] = 
                        $avanceAreasDeclaracionJurada[$claseTrabajador->trabajador->area] + 
                        $claseTrabajador->trabajador->archivos->contains($idArchivo) ? 1 : 0;
                }
                else{
                    $avanceAreasDeclaracionJurada[$claseTrabajador->trabajador->area] = $claseTrabajador->trabajador->archivos->contains($idArchivo) ? 1 : 0;
                }

                //Calcula el total de declaraciones juradas que deben existir por area
                if(isset($totalArea[$claseTrabajador->trabajador->area])){
                    $totalArea[$claseTrabajador->trabajador->area] = $totalArea[$claseTrabajador->trabajador->area] + 1;
                }
                else{
                    $totalArea[$claseTrabajador->trabajador->area] = 1;
                }
            }

            error_log("ARCHIVOOS");
            error_log(print_r($totalArchivos, true));

            foreach($totalArea as $area => $cantidad){
                if($cantidad > 0){
                    $p = round($avanceAreasDeclaracionJurada[$area] / $cantidad * 100, 2);
                    $avanceAreasDeclaracionJurada_[] = ['name' => $area, 'value' => $p];
                }
                else{
                    $avanceAreasDeclaracionJurada_[] = ['name' => $area, 'value' => 0];
                }
            }

            $data = [
                'avanceDeclaracionJurada' => [
                    ['name' => 'Finalizado', 'value' => $totalAceptados],
                    ['name' => 'Pendiente', 'value' => $totalArchivos - $totalAceptados],
                ],
                'avanceAreasDeclaracionJurada' => $avanceAreasDeclaracionJurada_,
            ];

        } catch (QueryException $e) {
            throw $e;
        }

        return $data;
    }

    // Deprecated
    public function documentacionDataReport_($idEmpresa)
    {
        $totalArchivos = 0;
        $totalDescargados = 0;
        $totalAceptados = 0;
        $trabajadoresArea = [];
        $avanceAreasDescarga = [];
        $avanceAreasDeclaracionJurada = [];
        $avanceAreasDescarga_ = [];
        $avanceAreasDeclaracionJurada_ = [];

        try {
            $empresa = EmpresaCliente::find($idEmpresa);
            $periodo = $empresa->periodos()->where('activo', 1)->first();
            $clases = $periodo->clases()->where('tipo', 'documentacion')->get();

            foreach ($clases as $clase) { 
                //Obtener un total de archivos en base al total de total de total de trabajadores
                $totalArchivos += $clase->archivos->count() * $clase->trabajadores->count();

                foreach($clase->trabajadores as $trabajador){
                    if(isset($trabajadoresArea[$trabajador->area])){
                        $trabajadoresArea[$trabajador->area] = $trabajadoresArea[$trabajador->area] + 1;
                    }
                    else{
                        $trabajadoresArea[$trabajador->area] = 1;
                    }
                }

                foreach ($clase->archivos as $archivo) {
                    foreach($archivo->trabajadores as $trabajador){
                        $totalDescargados += $trabajador->archivos_trabajadores->descargado;
                        $totalAceptados += $trabajador->archivos_trabajadores->aceptado;
                
                        if(array_search($trabajador->area, $avanceAreasDescarga)){
                            $avanceAreasDescarga[$trabajador->area] = $avanceAreasDescarga[$trabajador->area] + 1;
                        }
                        else{
                            $avanceAreasDescarga[$trabajador->area] = 1;
                        }

                        if(array_search($trabajador->area, $avanceAreasDeclaracionJurada)){
                            $avanceAreasDeclaracionJurada[$trabajador->area] = $avanceAreasDeclaracionJurada[$trabajador->area] + 1;
                        }
                        else{
                            $avanceAreasDeclaracionJurada[$trabajador->area] = 1;
                        }
                    }
                }
            }

            foreach ($trabajadoresArea as $area => $cantidad) {
                if(isset($avanceAreasDescarga[$area]) && $avanceAreasDescarga[$area] > 0){
                    $p = round($avanceAreasDescarga[$area] / $cantidad * 100, 2);
                    $avanceAreasDescarga_[] = ['name' => $area, 'value' => $p];
                }
                else{
                    $avanceAreasDescarga_[] = ['name' => $area, 'value' => 0];
                }

                if(isset($avanceAreasDeclaracionJurada[$area]) && $avanceAreasDeclaracionJurada[$area] > 0){
                    $p = round($avanceAreasDeclaracionJurada[$area] / $cantidad * 100, 2);
                    $avanceAreasDeclaracionJurada_[] = ['name' => $area, 'value' => $p];
                }
                else{
                    $avanceAreasDeclaracionJurada_[] = ['name' => $area, 'value' => 0];
                }
            }

            $data = [
                'avanceDescarga' => [
                    ['name' => 'Finalizado', 'value' => $totalDescargados],
                    ['name' => 'Pendiente', 'value' => $totalArchivos - $totalDescargados],
                ],
                'avanceDeclaracionJurada' => [
                    ['name' => 'Finalizado', 'value' => $totalAceptados],
                    ['name' => 'Pendiente', 'value' => $totalArchivos - $totalAceptados],
                ],
                'avanceAreasDescarga' => $avanceAreasDescarga_,
                'avanceAreasDeclaracionJurada' => $avanceAreasDeclaracionJurada_,
            ];

        } catch (QueryException $e) {
            throw $e;
        }

        return $data;
    }

    public function induccionInforme(Request $request, ResponseInterface $response, array $args)
    {
        $status = self::HTTP_OK;
        $idClase = $args['id'];

        
        try {
            $aprobados = array();

            //Obtener la lista de trabajadores de la clase
            $clase = Clase::find($idClase);
            $trabajadores = $clase->trabajadores;
            
            //Obtener la lista de aprobados de la clase
            foreach($trabajadores as $trabajador){
                $count = ExamenAzar::where('id_clase_trabajador', $trabajador->clases_trabajadores->id)->where('aprobado', 1)->count();
                if($count > 0){
                    $aprobados[] = $trabajador;
                }
            }

            //Obtener empresa cliente
            $cliente = $trabajadores[0]->empresaCliente;

            $plantillaInduccion = __DIR__ . '/../../../templates/reports/plantilla_induccion.xlsx';
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($plantillaInduccion);
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            $sheet->setCellValue('F7', $clase['titulo']);
            $sheet->setCellValue('A5', $cliente['razon_social']);
            $sheet->setCellValue('D5', $cliente['ruc']);
            $sheet->setCellValue('E5', $cliente['direccion']);
            $sheet->setCellValue('H5', $cliente['numero_trabajadores']);
            $sheet->setCellValue('F9', date('d/m/Y'));
            
            $row = 13;
            foreach ($aprobados as $trabajador) {
                $this-> insertSignature($trabajador, $row, $spreadsheet);
                $sheet->setCellValue('B' . $row, $trabajador['nombres'] . " " . $trabajador['apellidos']);
                $sheet->setCellValue('E' . $row, $trabajador['dni']);
                $sheet->setCellValue('F' . $row, $trabajador['puesto'] . " / " . $trabajador['area']);
                $row++;
            }

            $logoName = substr($cliente['logo'], strrpos($cliente['logo'], '/'));
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath(dirname(__DIR__).'/../../uploads/logos/' . $logoName); 
            $drawing->setCoordinates('A1');
            $drawing->setHeight(30);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
            $spreadsheet->removeSheetByIndex(0);
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $basename = bin2hex(random_bytes(8));
            $filename = sprintf('induccion-%s.%0.8s', $basename, 'xlsx');
            
            $writer->save($filename);
            
            $fh = fopen($filename, 'rb');
            $file_stream = new Stream($fh);

            return $response->withBody($file_stream)
                ->withHeader('Content-Disposition', "attachment; filename=$filename;")
                ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->withHeader('Content-Length', filesize($filename));
                
        }
        catch(QueryException $e){
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la clase: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    public function insertSignature($trabajador, $row, $spreadsheet) {
        if ($trabajador['signature']) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath($trabajador['signature']);
            $drawing->setCoordinates('G' . $row);
            $imageWidth = 50;
            $imageHeight = 48;
            $drawing->setWidthAndHeight($imageWidth, $imageHeight);
    
            // Centrar la imagen en la celda
            $cellWidth = 100; // Ancho de la celda en píxeles
            $cellHeight = 50; // Altura de la celda en píxeles
            $drawing->setOffsetX(($cellWidth - $imageWidth) / 2);
            $drawing->setOffsetY(($cellHeight - $imageHeight) / 2);
    
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
        }
    }

    public function capacitacionInforme(Request $request, ResponseInterface $response, array $args){
        $status = self::HTTP_OK;
        $idClase = $args['id'];
        try {
            $aprobados = array();
            $clase = Clase::find($idClase);
            $trabajadores = $clase->trabajadores ?? array();

            if (count($trabajadores) == 0) {
                $cliente = $clase->periodo->empresaCliente;
                //ahora muestrame esa info
                $plantillaCapacitacion = __DIR__ . '/../../../templates/reports/plantilla_capacitacion.xlsx';
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                $spreadsheet = $reader->load($plantillaCapacitacion);
                $sheet = $spreadsheet->setActiveSheetIndex(1);

                $sheet->setCellValue('F7', $clase['titulo']);
                $sheet->setCellValue('A5', $cliente['razon_social']);
                $sheet->setCellValue('D5', $cliente['ruc']);
                $sheet->setCellValue('E5', $cliente['direccion']);
                $sheet->setCellValue('H5', $cliente['numero_trabajadores']);
                $sheet->setCellValue('F9', date('d/m/Y'));
                
                $row = 13;
                foreach ($aprobados as $trabajador) {
                    $sheet->setCellValue('B' . $row, $trabajador['nombres'] . " " . $trabajador['apellidos']);
                    $sheet->setCellValue('E' . $row, $trabajador['dni']);
                    $sheet->setCellValue('F' . $row, $trabajador['puesto'] . " / " . $trabajador['area']);
                    $row++;
                }

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $basename = bin2hex(random_bytes(8));
                $filename = sprintf('capacitacion-%s.%0.8s', $basename, 'xlsx');

                $writer->save($filename);
    
                $fh = fopen($filename, 'rb');
                $file_stream = new Stream($fh);
    
                return $response->withBody($file_stream)
                    ->withHeader('Content-Disposition', "attachment; filename=$filename;")
                    ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                    ->withHeader('Content-Length', filesize($filename));
            }

    
  
            //Obtener la lista de aprobados de la clase
            foreach($trabajadores as $trabajador){
                $count = ExamenAzar::where('id_clase_trabajador', $trabajador->clases_trabajadores->id)->where('aprobado', 1)->count();
                if($count > 0){
                    $aprobados[] = $trabajador;
                }
            }
     
            //Obtener empresa cliente
            $cliente = $trabajadores[0]->empresaCliente;

            $plantillaCapacitacion = __DIR__ . '/../../../templates/reports/plantilla_capacitacion.xlsx';
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($plantillaCapacitacion);
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            $sheet->setCellValue('F7', $clase['titulo']);
            $sheet->setCellValue('A5', $cliente['razon_social']);
            $sheet->setCellValue('D5', $cliente['ruc']);
            $sheet->setCellValue('E5', $cliente['direccion']);
            $sheet->setCellValue('H5', $cliente['numero_trabajadores']);
            $sheet->setCellValue('F9', date('d/m/Y'));
            
            $row = 13;
            foreach ($aprobados as $trabajador) {
                $sheet->setCellValue('B' . $row, $trabajador['nombres'] . " " . $trabajador['apellidos']);
                $sheet->setCellValue('E' . $row, $trabajador['dni']);
                $sheet->setCellValue('F' . $row, $trabajador['puesto'] . " / " . $trabajador['area']);
                $this-> insertSignature($trabajador, $row, $spreadsheet);
                $row++;
            }

            $logoName = substr($cliente['logo'], strrpos($cliente['logo'], '/'));
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath(dirname(__DIR__).'/../../uploads/logos/' . $logoName); 
            $drawing->setCoordinates('A1');
            $drawing->setHeight(30);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
            $spreadsheet->removeSheetByIndex(0);
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $basename = bin2hex(random_bytes(8));
            $filename = sprintf('capacitacion-%s.%0.8s', $basename, 'xlsx');
            
            $writer->save($filename);
            
            $fh = fopen($filename, 'rb');
            $file_stream = new Stream($fh);

            return $response->withBody($file_stream)
                ->withHeader('Content-Disposition', "attachment; filename=$filename;")
                ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->withHeader('Content-Length', filesize($filename));

        }
        catch(QueryException $e){
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener la clase: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }

    public function documentacionInforme(Request $request, ResponseInterface $response, array $args){
        $status = self::HTTP_OK;
        $idArchivo = $args['id'];

        try {
            $aceptados = array();

            //Obtener la clase a la que pertenece el archivo
            $archivo = Archivo::find($idArchivo);
            $clase = $archivo->clase;

            //Obtener lista de ArchivoTrabajador
            $archivosTrabajadores = ArchivoTrabajador::where('id_archivo', $idArchivo)->where('aceptado', 1)->get();
            if(!$archivosTrabajadores){
                $archivosTrabajadores = array();
            }

            //Obtener la lista de trabajadores que aceptaron el documento
            foreach($archivosTrabajadores as $archivoTrabajador){
                $aceptados[] = $archivoTrabajador->trabajador;
            }

            //Obtener empresa cliente
            $cliente = $clase->periodo->empresaCliente;

            $plantillaDocumentacion = __DIR__ . '/../../../templates/reports/plantilla_documentacion.xlsx';
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($plantillaDocumentacion);
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            $sheet->setCellValue('F7', $archivo['titulo']);
            $sheet->setCellValue('A5', $cliente['razon_social']);
            $sheet->setCellValue('D5', $cliente['ruc']);
            $sheet->setCellValue('E5', $cliente['direccion']);
            $sheet->setCellValue('H5', $cliente['numero_trabajadores']);
            $sheet->setCellValue('F9', date('d/m/Y'));

            
            $row = 13;
            foreach ($aceptados as $trabajador) {
                $this-> insertSignature($trabajador, $row, $spreadsheet);
                $sheet->setCellValue('B' . $row, $trabajador['nombres'] . " " . $trabajador['apellidos']);
                $sheet->setCellValue('E' . $row, $trabajador['dni']);
                $sheet->setCellValue('F' . $row, $trabajador['puesto'] . " / " . $trabajador['area']);
                $row++;
            }

            $logoName = substr($cliente['logo'], strrpos($cliente['logo'], '/'));
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath(dirname(__DIR__).'/../../uploads/logos/' . $logoName); 
            $drawing->setCoordinates('A1');
            //$drawing->setHeight(30);
            $drawing->setWidth(100);
            $drawing->setWorksheet($spreadsheet->getActiveSheet());
            $spreadsheet->removeSheetByIndex(0);
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $basename = bin2hex(random_bytes(8));
            $filename = sprintf('sistemagestionSST-%s.%0.8s', $basename, 'xlsx');
            
            $writer->save($filename);
            
            $fh = fopen($filename, 'rb');
            $file_stream = new Stream($fh);

            return $response->withBody($file_stream)
                ->withHeader('Content-Disposition', "attachment; filename=$filename;")
                ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->withHeader('Content-Length', filesize($filename));
        }
        catch(QueryException $e){
            $status = self::HTTP_BAD_REQUEST;
            $res = MessageResponse::getInstance($status, "Error al obtener el informe de capacitacion: " . $e->errorInfo[2], []);
        }

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()->write(json_encode($res, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK));

        return $response->withStatus($status);
    }
}