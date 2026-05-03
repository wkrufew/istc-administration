<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Ver listado de tickets.
     * Administrador/Secretaria/Admisión ven todos; el creador solo ve el suyo.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ver_tickets') || $user->can('ver_todos_tickets');
    }

    /**
     * Ver un ticket específico.
     * Quien tenga ver_todos_tickets siempre puede. El creador solo ve el suyo.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->can('ver_todos_tickets')) {
            return true;
        }

        return $user->can('ver_tickets') && $ticket->created_by === $user->id;
    }

    /**
     * Crear un ticket nuevo.
     */
    public function create(User $user): bool
    {
        return $user->can('crear_tickets');
    }

    /**
     * Responder (enviar mensaje) en un ticket.
     */
    public function reply(User $user, Ticket $ticket): bool
    {
        if (! $user->can('responder_tickets')) {
            return false;
        }

        // El ticket debe estar abierto
        return $ticket->estaAbierto();
    }

    /**
     * Cambiar el estado de un ticket.
     */
    public function changeStatus(User $user, Ticket $ticket): bool
    {
        return $user->can('cambiar_estado_tickets');
    }

    /**
     * Asignar un ticket a un usuario.
     */
    public function assign(User $user): bool
    {
        return $user->can('asignar_tickets');
    }

    /**
     * Editar título, prioridad, fechas y observaciones de un ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->can('gestionar_tickets');
    }

    /**
     * Cerrar o resolver un ticket definitivamente.
     */
    public function close(User $user): bool
    {
        return $user->can('cerrar_tickets');
    }
}
