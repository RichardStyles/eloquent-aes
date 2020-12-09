<?php

namespace RichardStyles\EloquentAES\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use RichardStyles\EloquentAES\EloquentAESFacade as EloquentAES;

class AESEncrypted implements CastsAttributes
{
    /**
     * Cast the given value and decrypt
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return $value;
        }
        
        return EloquentAES::decrypt($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return $value;
        }
        
        return EloquentAES::encrypt($value);
    }
}
