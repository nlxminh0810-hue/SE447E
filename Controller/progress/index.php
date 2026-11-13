<?php

// Progress Controller - Quản lý học tập

Auth::requireLogin();

$user = Auth::getUser();
$displayName = isset($user['full_name']) && $user['full_name'] !== ''
    ? $user['full_name']
    : (isset($user['username']) ? $user['username'] : 'Học viên');

$learningOverview = [
    'totalCourses' => 4,
    'completedCourses' => 1,
    'activeCourses' => 2,
    'avgScore' => 82,
    'studyStreak' => 12,
    'studyHours' => 36,
];

$activeCourses = [
    [
        'title' => 'TOEIC Intensive',
        'progress' => 68,
        'nextLesson' => 'Listening Drill: Short Talks',
        'deadline' => '20/05/2026',
        'score' => 78,
    ],
    [
        'title' => 'Speaking Skills',
        'progress' => 42,
        'nextLesson' => 'Speaking Challenge #2',
        'deadline' => '28/05/2026',
        'score' => 85,
    ],
];

$weeklyPlan = [
    ['day' => 'Thứ 2', 'task' => 'TOEIC Reading Practice', 'status' => 'done'],
    ['day' => 'Thứ 3', 'task' => 'Vocabulary Review', 'status' => 'done'],
    ['day' => 'Thứ 4', 'task' => 'Listening Mini Test', 'status' => 'today'],
    ['day' => 'Thứ 5', 'task' => 'Speaking Record Task', 'status' => 'upcoming'],
    ['day' => 'Thứ 6', 'task' => 'Full Practice Quiz', 'status' => 'upcoming'],
];

$recentTests = [
    ['name' => 'Quiz Chuong 1', 'score' => 80, 'date' => '07/05/2026', 'passed' => true],
    ['name' => 'Vocabulary Test #3', 'score' => 73, 'date' => '06/05/2026', 'passed' => true],
    ['name' => 'Listening Checkpoint', 'score' => 64, 'date' => '04/05/2026', 'passed' => false],
];

$skillProgress = [
    ['name' => 'Reading', 'percent' => 76],
    ['name' => 'Listening', 'percent' => 61],
    ['name' => 'Writing', 'percent' => 58],
    ['name' => 'Speaking', 'percent' => 69],
];

require_once 'View/progress/index.php';
