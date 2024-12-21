<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCataloguePermission extends Model
{
    use HasFactory;

    protected $table = 'user_catalogue_permission';

    public function user_catalogues(){
        return $this->beLongsTo(UserCatalogue::class, 'user_catalogue_id');
    }

    public function permissions(){
        return $this->beLongsTo(Permission::class, 'permission_id');
    }
    

}
