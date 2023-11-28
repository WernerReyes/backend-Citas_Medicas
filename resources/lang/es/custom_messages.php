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
];

return $customMessages;