<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class EmpresaCliente.
 * 
 * @author Adolfo Julcamoro <julcamorosm@gmail.com>
 * @version 1.0
 * @package App\Application\Models
 * @subpackage Controllers
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/adolfojulcamoro
 * @since 1.0
 */
class EmpresaCliente extends Model
{
    protected $table = 'empresas_cliente';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'razon_social', 
        'direccion', 
        'telefono', 
        'ruc', 
        'correo', 
        'numero_trabajadores', 
        'responsable',
        'logo',
    ];

    public function periodos(): HasMany
    {
        return $this->hasMany(Periodo::class, 'id_empresa_cliente', 'id');
    }

    public function trabajadores(): HasMany
    {
        return $this->hasMany(Trabajador::class, 'id_empresa_cliente', 'id');
    }
}