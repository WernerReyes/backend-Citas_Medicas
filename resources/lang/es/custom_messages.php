<?php 
$customMessages = [
    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El campo :attribute debe ser una dirección de correo válida.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'numeric' => 'El campo :attribute debe ser mínimo :min.',
    ],
    'max' => [
        'string' => 'El campo :attribute debe tener como maximo :max caracteres.',
        'numeric' => 'El campo :attribute debe ser maximo :max.',
    ],
    'numeric' => 'El campo :attribute debe contener solo números',
    'confirmed' => 'La confirmación del campo :attribute no coincide.',
    'unique' => 'El campo :attribute ya esta en uso',
    'image' => 'El campo :attribute debe ser una imagen.',
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'password.regex' => 'La contraseña debe contener al menos 8 caracteres, una letra minúscula, una letra mayúscula y un número.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'after_or_equal' => 'No se puede seleccionar una fecha anterior a la actual.',
    'after' => 'El campo :attribute debe ser una fecha posterior a la fecha de inicio.',
    'date_format' => 'El campo :attribute debe tener el formato: :format.',
    'exists' => 'El campo :attribute no existe.',
    'in' => 'El campo :attribute debe ser uno de los siguientes tipos: :values.',
    'before' => 'El campo :attribute debe ser una fecha anterior a la fecha actual.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a la fecha actual.',
    
];

return $customMessages;