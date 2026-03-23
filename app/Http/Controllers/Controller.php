<?php

namespace App\Http\Controllers;

/**
 * Base Controller for the BellaVella API.
 * 
 * Pattern: Foundation Hardening Phase 4
 * - Controllers should prioritize returning Eloquent Resources or Resource Collections.
 * - Raw array responses are discouraged for entity data to prevent contract drift.
 * - Business logic should be encapsulated in Services where it exceeds simple CRUD.
 */
abstract class Controller
{
}

