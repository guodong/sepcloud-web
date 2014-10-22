<?php
class Vm extends Eloquent{
    protected $table = 'vm';
    protected $fillable = array('uuid', 'name', 'cpu', 'memory', 'disk', 'os', 'xml', 'spiceport', 'user_id');
    
    public function user()
    {
        return $this->belongsTo('User');
    }
}