<?php
/*
Plugin Name: Endpoint de creación de posts
Description: Agrega un endpoint para crear nuevos posts mediante una petición POST externa.
Author: Simon Agredo
Version: 1.0
*/

// Registrar el endpoint utilizando el hook 'rest_api_init'
add_action('rest_api_init', 'register_custom_endpoint');

function register_custom_endpoint() {
    // Ruta del endpoint: /wp-json/custom/v1/create-post
    register_rest_route('custom/v1', '/create-post', array(
        'methods' => 'POST',
        'callback' => 'create_post_callback',
    ));
}

// Función de callback para crear el post
function create_post_callback($request) {
    // Obtener los datos de la petición POST
    $post_data = $request->get_params();

    // Validar los datos recibidos (aquí puedes agregar tus propias validaciones)
    if (empty($post_data['title']) || empty($post_data['content'])) {
        return new WP_Error('empty_data', 'Por favor, proporciona un título y contenido para el post.', array('status' => 400));
    }

    // Crear un nuevo post utilizando 'wp_insert_post'
    $new_post = array(
        'post_title' => sanitize_text_field($post_data['title']),
        'post_content' => wp_kses_post($post_data['content']),
        'post_status' => 'publish',
    );

    $post_id = wp_insert_post($new_post);

    if ($post_id) {
        return array('message' => 'Post creado con éxito.', 'post_id' => $post_id);
    } else {
        return new WP_Error('creation_failed', 'No se pudo crear el post.', array('status' => 500));
    }
}
