<?php

return [
    // General messages
    'success' => 'Success',
    'error' => 'An error occurred',
    'not_found' => 'Not found',
    'unauthorized' => 'Unauthorized access',
    'validation_error' => 'Invalid data',
    'server_error' => 'Server error',

    // Authentication
    'auth' => [
        'login_success' => 'Login successful',
        'login_failed' => 'Invalid email or password',
        'logout_success' => 'Logout successful',
        'register_success' => 'Registration successful',
        'register_failed' => 'Registration failed',
        'token_invalid' => 'Invalid token',
        'token_expired' => 'Token has expired',
        'email_required' => 'Email is required',
        'password_required' => 'Password is required',
        'name_required' => 'Name is required',
        'email_invalid' => 'Invalid email format',
        'password_min' => 'Password must be at least 6 characters',
        'email_exists' => 'Email already exists',
    ],

    // Game
    'game' => [
        'id_check_success' => 'Game ID found',
        'id_check_failed' => 'Game ID not found',
        'id_required' => 'Game ID is required',
        'zone_id_required' => 'Zone ID is required for this game',
        'game_code_required' => 'Game code is required',
        'game_not_supported' => 'Game not supported',
        'provider_error' => 'Provider error',
        'all_providers_failed' => 'All providers failed',
        'username_found' => 'Username found: :username',
        'checking_id' => 'Checking game ID...',
    ],

    // Transaction
    'transaction' => [
        'topup_success' => 'Top-up successful',
        'topup_failed' => 'Top-up failed',
        'topup_pending' => 'Top-up is being processed',
        'amount_required' => 'Amount is required',
        'product_required' => 'Product must be selected',
        'insufficient_balance' => 'Insufficient balance',
        'transaction_not_found' => 'Transaction not found',
        'transaction_cancelled' => 'Transaction cancelled',
    ],

    // Provider
    'provider' => [
        'apigames' => 'ApiGames',
        'codashop' => 'Codashop',
        'duniagames' => 'Dunia Games',
        'not_supported' => 'Provider not supported',
        'connection_error' => 'Failed to connect to provider',
        'timeout' => 'Provider connection timeout',
    ],

    // Games
    'games' => [
        'mobilelegend' => 'Mobile Legends',
        'mobilelegends' => 'Mobile Legends: Bang Bang',
        'freefire' => 'Free Fire',
        'pubgmobile' => 'PUBG Mobile',
        'valorant' => 'Valorant',
        'genshinimpact' => 'Genshin Impact',
        'aov' => 'Arena of Valor',
        'wildrift' => 'League of Legends: Wild Rift',
        'honkaiimpact' => 'Honkai Impact 3rd',
        'ragnarokmobile' => 'Ragnarok M: Eternal Love',
        'pointblank' => 'Point Blank',
    ],

    // Fields
    'fields' => [
        'user_id' => 'User ID',
        'zone_id' => 'Zone ID',
        'server_id' => 'Server ID',
        'character_name' => 'Character Name',
        'email' => 'Email',
        'phone' => 'Phone Number',
    ],

    // API Messages
    'api' => [
        'welcome' => 'Welcome to PPOB Game API',
        'version' => 'API Version: :version',
        'documentation' => 'Documentation available at: :url',
        'rate_limit_exceeded' => 'Rate limit exceeded',
        'invalid_request' => 'Invalid request',
        'missing_parameter' => 'Parameter :parameter is required',
        'invalid_parameter' => 'Parameter :parameter is invalid',
    ],

    // Status
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
    ],
];
