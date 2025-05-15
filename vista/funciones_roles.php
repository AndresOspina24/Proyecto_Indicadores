<?php
// funciones_roles.php

function tieneRol($rolesRequeridos, $rolesDelUsuario) {
    if (!isset($rolesDelUsuario) || !is_array($rolesDelUsuario)) {
        return false; // O manejar el error como prefieras
    }
    foreach ($rolesDelUsuario as $rol) {
        if (in_array($rol->nombre, $rolesRequeridos)) {
            return true;
        }
    }
    return false;
}

function esAdmin($rolesDelUsuario) {
    return tieneRol(["admin"], $rolesDelUsuario);
}

function esVerificador($rolesDelUsuario) {
    return tieneRol(["Verificador"], $rolesDelUsuario);
}

function esValidador($rolesDelUsuario) {
    return tieneRol(["Validador"], $rolesDelUsuario);
}
?>