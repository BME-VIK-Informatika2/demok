<?php

require __DIR__ . '/../vendor/autoload.php';

class Vevo extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'vevok';
    public $timestamps = false;

    public function megrendelesek(){
        return $this->hasMany(Megrendeles::class);
    }
}

class Termek extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'termekek';
    public $timestamps = false;

    public function megrendeles_tetelek(){
        return $this->hasMany(MegrendelesTetel::class);
    }

    public function megrendelesek(){
        return $this->belongsToMany(Megrendeles::class, 'megrendeles_tetelek')->withPivot('db');
    }

    public function keszlet()
    {
        return $this->raktarkeszlet - $this->megrendeles_tetelek()->sum('db');
    }
}

class Megrendeles extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'megrendelesek';
    public $timestamps = false;

    public function vevo()
    {
        return $this->belongsTo(Vevo::class);
    }

    public function megrendeles_tetelek(){
        return $this->hasMany(MegrendelesTetel::class);
    }

    public function termekek(){
        return $this->belongsToMany(Termek::class, 'megrendeles_tetelek')->withPivot('db');
    }
}

class MegrendelesTetel extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'megrendeles_tetelek';
    public $timestamps = false;

    public function megrendeles()
    {
        return $this->belongsTo(Megrendeles::class);
    }

    public function termek()
    {
        return $this->belongsTo(Termek::class);
    }
}