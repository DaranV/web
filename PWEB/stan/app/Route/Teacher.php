<?php
use Core\Router;


Router::get('groups', ["controller" => "Teacher\GroupController", "method" => "index", "name" => "group_index", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('group/(:num)/(:num)', ["controller" => "Teacher\GroupController", "method" => "view", "name" => "group_view", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::post('ajax/group/add', ["controller" => "Teacher\GroupController", "method" => "ajax_add", "name" => "ajax_add_group", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::get('ajax/group/(:num)/delete', ["controller" => "Teacher\GroupController", "method" => "ajax_delete", "name" => "ajax_delete_group", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/group/edit', ["controller" => "Teacher\GroupController", "method" => "ajax_edit", "name" => "ajax_edit_group", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/group/add/student', ["controller" => "Teacher\GroupController", "method" => "ajax_add_student", "name" => "ajax_add_student_group", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::get('ajax/group/(:num)/remove/student/(:num)', ["controller" => "Teacher\GroupController", "method" => "ajax_remove_student", "name" => "ajax_remove_student_group", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);


Router::get('students', ["controller" => "Teacher\StudentController", "method" => "index", "name" => "student_index", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('student/(:num)/(:slug)', ["controller" => "Teacher\StudentController", "method" => "view", "name" => "student_view", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('student/(:num)/details/(:num)', ["controller" => "Teacher\StudentController", "method" => "view_bilan", "name" => "student_view_bilan", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('ajax/student/(:num)/delete', ["controller" => "Teacher\StudentController", "method" => "ajax_delete", "name" => "ajax_delete_student", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/student/edit', ["controller" => "Teacher\StudentController", "method" => "ajax_edit", "name" => "ajax_edit_student", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/student/edit/password', ["controller" => "Teacher\StudentController", "method" => "ajax_edit_password", "name" => "ajax_edit_password_student", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);


Router::get('tests', ["controller" => "Teacher\TestController", "method" => "index", "name" => "test_index", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('test/(:num)/(:slug)', ["controller" => "Teacher\TestController", "method" => "view", "name" => "test_view", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('test/(:num)/(:slug)/ended', ["controller" => "Teacher\TestController", "method" => "ended_view", "name" => "test_ended_view", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('test/add', ["controller" => "Teacher\TestController", "method" => "add", "name" => "test_add", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('test/(:num)/(:slug)/editor', ["controller" => "Teacher\TestController", "method" => "editor_view", "name" => "test_editor_view", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('test/(:num)/(:slug)/live', ["controller" => "Teacher\TestController", "method" => "live_editor_view", "name" => "test_live_editor_view", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('test/start/(:num)/(:slug)', ["controller" => "Teacher\TestController", "method" => "test_start", "name" => "test_start", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('test/stop/(:num)/(:slug)', ["controller" => "Teacher\TestController", "method" => "test_stop", "name" => "test_stop", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('ajax/test/(:num)/delete', ["controller" => "Teacher\TestController", "method" => "ajax_delete", "name" => "ajax_delete_test", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/test/add', ["controller" => "Teacher\TestController", "method" => "ajax_add", "name" => "ajax_add_test", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/test/edit', ["controller" => "Teacher\TestController", "method" => "ajax_edit", "name" => "ajax_edit_test", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/test/add/question', ["controller" => "Teacher\TestController", "method" => "ajax_add_question", "name" => "ajax_add_question_test", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/test/remove/question', ["controller" => "Teacher\TestController", "method" => "ajax_remove_question", "name" => "ajax_remove_question_test", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::get('test/preview/(:num)/(:num)', ["controller" => "Teacher\TestController", "method" => "test_preview", "name" => "test_preview", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('test/preview/q/(:num)', ["controller" => "Teacher\TestController", "method" => "test_preview_questions", "name" => "test_preview_questions", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('ajax/test/(:num)/onlines', ["controller" => "Teacher\TestController", "method" => "ajax_list_onlines", "name" => "ajax_test_list_onlines", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::get('ajax/test/(:num)/live-stats', ["controller" => "Teacher\TestController", "method" => "ajax_test_live_stats", "name" => "ajax_test_live_stats", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/test/start/question', ["controller" => "Teacher\TestController", "method" => "ajax_start_question", "name" => "ajax_test_start_question", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/test/stop/question', ["controller" => "Teacher\TestController", "method" => "ajax_stop_question", "name" => "ajax_test_stop_question", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);

Router::get('themes', ["controller" => "Teacher\ThemeController", "method" => "index", "name" => "theme_index", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('theme/(:num)/(:slug)', ["controller" => "Teacher\ThemeController", "method" => "view", "name" => "theme_view", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::post('ajax/theme/add', ["controller" => "Teacher\ThemeController", "method" => "ajax_add", "name" => "ajax_add_theme", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::get('ajax/theme/(:num)/delete', ["controller" => "Teacher\ThemeController", "method" => "ajax_delete", "name" => "ajax_delete_theme", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/theme/edit', ["controller" => "Teacher\ThemeController", "method" => "ajax_edit", "name" => "ajax_edit_theme", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/theme/add/question', ["controller" => "Teacher\ThemeController", "method" => "ajax_add_question", "name" => "ajax_add_question_theme", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::get('ajax/theme/(:num)/remove/question/(:num)', ["controller" => "Teacher\ThemeController", "method" => "ajax_remove_question", "name" => "ajax_remove_question_theme", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);

Router::get('questions', ["controller" => "Teacher\QuestionController", "method" => "index", "name" => "question_index", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('ajax/question/(:num)/delete', ["controller" => "Teacher\QuestionController", "method" => "ajax_delete", "name" => "ajax_delete_question", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::get('question/(:num)/(:slug)', ["controller" => "Teacher\QuestionController", "method" => "view", "name" => "question_view", "middlewares" => ["AuthMiddleware", "IsProfMiddleware"]]);
Router::post('ajax/question/edit', ["controller" => "Teacher\QuestionController", "method" => "ajax_edit", "name" => "ajax_edit_question", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/question/edit/anwsers', ["controller" => "Teacher\QuestionController", "method" => "ajax_edit_answers", "name" => "ajax_edit_question_answer", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/question/add', ["controller" => "Teacher\QuestionController", "method" => "ajax_add", "name" => "ajax_add_question", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::get('ajax/question/(:num)', ["controller" => "Teacher\QuestionController", "method" => "ajaw_view", "name" => "ajax_view_question", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);


Router::post('ajax/edit/my-account', ["controller" => "TeacherController", "method" => "ajax_edit", "name" => "ajax_edit_my_account_prof", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
Router::post('ajax/edit/my-password', ["controller" => "TeacherController", "method" => "ajax_edit_password", "name" => "ajax_edit_my_password_prof", "middlewares" => ["AuthMiddleware", "IsProfMiddleware", "CorsMiddleware"]]);
