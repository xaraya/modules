<?php

return [
    'cd_loans' => [
        //'type' => 'workflow',
        'type' => 'state_machine',
        //'marking_store' => [
        //    'type' => 'method',
        //    'property' => 'marking'
        //],
        //'metadata' => [],
        'supports' => ['cdcollection'],  // DynamicData Object this workflow should apply to
        //'events_to_dispatch' => [],
        'initial_marking' => ['available'],
        'places' => [
            'available',
            'requested',
            'approved',
            'rejected',
            'escalated',
            'acknowledged',
            'retrieved',
            'returned',
            'deleted',
            'not available'
        ],
        'transitions' => [
            'request' => [
                // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Bundle/FrameworkBundle/DependencyInjection/FrameworkExtension.php#L917
                // we mean from ANY here, not from ALL like default for workflow
                //'from' => ['available', 'requested', 'rejected', 'escalated', 'acknowledged', 'returned'],
                'from' => ['available'],
                'to' => ['requested']
            ],
            'approve' => [
                'from' => 'requested',
                'to' => 'approved'
            ],
            'retrieve' => [
                'from' => 'approved',
                'to' => ['retrieved', 'not available']  // not supported for state_machine, pick the first
            ],
            'reject' => [
                'from' => 'requested',
                'to' => 'rejected'
            ],
            'acknowledge' => [
                'from' => 'rejected',
                'to' => 'acknowledged'
            ],
            'escalate' => [
                'from' => 'requested',
                'to' => 'escalated'
            ],
            'timeout' => [
                'from' => 'escalated',
                'to' => 'rejected'
            ],
            'return' => [
                'from' => 'retrieved',
                'to' => ['returned', 'available']  // not supported for state_machine, pick the first
            ],
            'close' => [
                'from' => ['returned', 'acknowledged'],
                'to' => 'deleted'
            ],
        ],
    ]
];
