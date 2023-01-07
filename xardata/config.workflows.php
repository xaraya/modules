<?php

// See config/packages/workflow.yaml at https://symfony.com/doc/current/workflow.html
// and corresponding config/workflow.php at https://github.com/zerodahero/laravel-workflow
// See also https://pimcore.com/docs/pimcore/current/Development_Documentation/Workflow_Management/Configuration_Details/index.html

sys::import('modules.workflow.class.handlers');

// list of callback functions per workflow, transition & event type
$callbackFuncs = [
    // here you can specify callback functions as transition blockers - expression language is not supported
    'cd_loans.request.guard' => xarWorkflowHandlers::guardPropertyHandler([
        'cdcollection' => ['status' => 'available'],
    ]),
    // here you can specify callback functions to update the actual objects once the transition is completed
    'cd_loans.retrieve.completed' => xarWorkflowHandlers::updatePropertyHandler([
        'cdcollection' => ['status' => 'not available'],
    ]),
    'cd_loans.return.completed' => xarWorkflowHandlers::updatePropertyHandler([
        'cdcollection' => ['status' => 'available'],
    ])
];

// return configuration of the workflow(s)
return [
    'cd_loans' => [
        'label' => 'Music CD Loans',
        'description' => "Borrow CD's, browse CDs, etc...",
        //'class' => null,  // something other than Workflow or StateMachine - not supported here
        //'type' => 'workflow',
        'type' => 'state_machine',
        //'marking_store' => [
        //    'type' => 'method',
        //    'property' => 'marking'  // this assumes the subject has methods getMarking() and setMarking()
        //],
        //'metadata' => [],
        'supports' => ['cdcollection'],  // DynamicData Object this workflow should apply to
        // you can pass one or more event names, or pass an empty array to not dispatch any event
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
        // @todo custom templates to use for particular places?
        'templates' => [
        ],
        'transitions' => [
            'request' => [
                // See https://github.com/symfony/symfony/blob/6.3/src/Symfony/Bundle/FrameworkBundle/DependencyInjection/FrameworkExtension.php#L917
                // we mean from ANY here, not from ALL like default for workflow
                //'from' => ['available', 'requested', 'rejected', 'escalated', 'acknowledged', 'returned'],
                'from' => ['available'],
                'to' => ['requested'],
                // here you can specify callback functions as transition blockers - expression language is not supported
                'guard' => $callbackFuncs['cd_loans.request.guard']
            ],
            'approve' => [
                'from' => 'requested',
                'to' => 'approved'
            ],
            'retrieve' => [
                'from' => 'approved',
                'to' => ['retrieved', 'not available'],  // not supported for state_machine, pick the first
                // here you can specify callback functions to update the actual objects once the transition is completed
                'completed' => $callbackFuncs['cd_loans.retrieve.completed']
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
                'to' => ['returned', 'available'],  // not supported for state_machine, pick the first
                // here you can specify callback functions to update the actual objects once the transition is completed
                'completed' => $callbackFuncs['cd_loans.return.completed']
            ],
            'close' => [
                'from' => ['returned', 'acknowledged'],
                'to' => 'deleted'
            ],
        ],
    ]
];
