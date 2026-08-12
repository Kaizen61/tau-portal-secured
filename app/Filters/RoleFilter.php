<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\ActivityLogger;

class RoleFilter implements FilterInterface
{
    // FIX (A10 - fail closed): deny by default. Access is granted only if
    // the session role exactly matches the role(s) passed in from the
    // route definition - the opposite of the vulnerable version's
    // "if not student, allow" logic.
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $role = $session->get('role');
        $allowedRoles = $arguments ?? [];

        if (!$role || !in_array($role, $allowedRoles, true)) {
            $logger = new ActivityLogger();
            $logger->log(
                $session->get('user_id'),
                $session->get('username') ?? $session->get('student_number'),
                'ACCESS_DENIED',
                'Attempted to access ' . current_url() . ' without the required role.',
                'WARNING'
            );

            return redirect()->to('/')->with('error', 'Please log in to continue.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after the request.
    }
}
