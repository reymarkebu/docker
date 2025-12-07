<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Configuration Example
    |--------------------------------------------------------------------------
    |
    | This is an example of a custom configuration option that you can add to
    | your Laravel application. You can define any settings you need here
    | and access them throughout your application using the config helper.
    |
    */

    'email_statuses' => [
        ['id' => 1, 'label' => 'create proposal'],
        ['id' => 2, 'label' => 'proposal created'],
        ['id' => 3, 'label' => 'invalid email'],
    ],

    'phone_call_statuses' => [
        ['id' => 1, 'label' => 'Call now'],
        ['id' => 2, 'label' => 'Follow-up the next day'],
        ['id' => 3, 'label' => 'Follow-up next week'],
        ['id' => 4, 'label' => 'Follow-up next month'],
        ['id' => 5, 'label' => 'Follow-up after 3 months'],
        ['id' => 6, 'label' => 'Follow-up after 6 months'],
        ['id' => 7, 'label' => 'Cannot be reach'],
        ['id' => 8, 'label' => 'Invalid number'],
    ],

    'island_groups' => [
        ['id' => 1, 'label' => 'Luzon'],
        ['id' => 2, 'label' => 'Visayas'],
        ['id' => 3, 'label' => 'Mindanao'],
    ],

    'account_types' => [
        'prospect' => 'prospect',
        'lead' => 'lead',
        'account' => 'account'
    ],

    'proposal_types' => [
        ['id' => 1, 'label' => 'Create Proposal'],
        ['id' => 2, 'label' => 'Individual'],
        ['id' => 3, 'label' => 'Family'],
        ['id' => 4, 'label' => 'Maxicare Starter'],
        ['id' => 5, 'label' => 'Maxicare Plus Group'],
        ['id' => 6, 'label' => 'Maxicare Plus Small'],
        ['id' => 7, 'label' => 'PCAF Requested'],
    ],

    'plan_types' => [
        ['id' => 1, 'label' => 'Individual'],
        ['id' => 2, 'label' => 'Family'],
        ['id' => 3, 'label' => 'Maxicare Starter'],
        ['id' => 4, 'label' => 'Maxicare Plus Group'],
        ['id' => 5, 'label' => 'Maxicare Plus Small'],
        ['id' => 6, 'label' => 'Corporate'],
    ],

];
