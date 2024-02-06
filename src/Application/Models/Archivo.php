<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Archivo.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class Archivo extends Model
{
    protected $table = 'archivos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['id_clase', 'titulo', 'descripcion', 'url', 'extension', 'tipo', 'imagen', 'visto'];

    
    /**
     * Returns a BelongsTo relationship between this object and the Clase model, 
     * using the fields 'id_clase' and 'id' as the keys.
     *
     * @return BelongsTo
     */
    public function clase(): BelongsTo
    {
        return $this->belongsTo(Clase::class, 'id_clase', 'id');
    }

    public function declaracionJuradda(): HasOne
    {
        return $this->hasOne(DeclaracionJurada::class, 'id_archivo', 'id');
    }

    public function trabajadores(): BelongsToMany
    {
        return $this->belongsToMany(Trabajador::class, 'archivos_trabajadores', 'id_archivo', 'id_trabajador')
        ->withPivot('id', 'descargado', 'aceptado')->as('archivos_trabajadores');
    }

}