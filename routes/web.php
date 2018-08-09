<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::POST('/startWork', 'HomeController@startWork');
Route::POST('/stopWork', 'HomeController@stopWork');
Route::POST('/register_hour', 'WorkHoursController@registerHour');

//********************AJAX*********************** */
Route::post('/userPrivilage', 'AdminController@userPrivilagesAjax')->name('api.privilageAjax');
Route::post('/userPrivilagesAjax', 'AdminController@userPrivilagesAjaxData')->name('api.privilageAjaxData');

Route::post('/delete_picture', 'AuditController@delete_picture')->name('api.delete_picture');
Route::post('/add', 'AuditController@ajax')->name('api.ajax');
Route::post('auditScoreAjax', 'AuditController@auditScoreAjax')->name('api.scores');
Route::post('/showAudits', 'AuditController@showAuditsPost')->name('api.auditTable');
Route::post('/editAudit', 'AdminController@editAuditPost')->name('api.editAudit');

Route::POST('/datatableAcceptHour', 'WorkHoursController@datatableAcceptHour')->name('api.acceptHour');
Route::POST('/datatableAcceptHourCadre', 'WorkHoursController@datatableAcceptHourCadre')->name('api.acceptHourCadre');
Route::POST('/datatableCheckList', 'WorkHoursController@datatableCheckList')->name('api.checkList');
Route::POST('/saveAcceptHour', 'WorkHoursController@saveAcceptHour')->name('api.saveAcceptHour');
Route::POST('/deleteAcceptHour', 'WorkHoursController@deleteAcceptHour')->name('api.deleteAcceptHour');
Route::POST('/editAcceptHour', 'WorkHoursController@editAcceptHour')->name('api.editAcceptHour');
Route::POST('/addAcceptHour', 'WorkHoursController@addAcceptHour')->name('api.addAcceptHour');


Route::POST('/uniqueUsername', 'UsersController@uniqueUsername')->name('api.uniqueUsername');
Route::POST('/uniquerEmail', 'UsersController@uniqueEmail')->name('api.uniqueEmail');
Route::POST('/uniquerEmailEdit', 'UsersController@uniquerEmailEdit')->name('api.uniquerEmailEdit');
Route::POST('/uniquePBX', 'UsersController@uniquePBX')->name('api.uniquePBX');
Route::POST('/datatableEmployeeManagement', 'UsersController@datatableEmployeeManagement')->name('api.datatableEmployeeManagement');

Route::POST('/datatableCadreManagement', 'UsersController@datatableCadreManagement')->name('api.datatableCadreManagement');
Route::POST('/datatableCadreManagementFire', 'UsersController@datatableCadreManagementFire')->name('api.datatableCadreManagementFire');


Route::POST('/datatableDkjRaport', 'DkjController@datatableDkjRaport')->name('api.datatableDkjRaport');
Route::POST('/getUser', 'DkjController@getUser')->name('api.getUser');
Route::POST('/dkjRaportSave', 'DkjController@dkjRaportSave')->name('api.dkjRaportSave');

Route::POST('/datatableDkjVerification', 'DkjController@datatableDkjVerification')->name('api.datatableDkjVerification');
Route::POST('/saveDkjVerification', 'DkjController@saveDkjVerification')->name('api.saveDkjVerification');
Route::POST('/datatableShowDkjVerification', 'DkjController@datatableShowDkjVerification')->name('api.datatableShowDkjVerification');
Route::POST('/datatableDkjShowEmployee', 'DkjController@datatableDkjShowEmployee')->name('api.datatableDkjShowEmployee');

Route::POST('/datatableCadreHR', 'UsersController@datatableCadreHR')->name('api.datatableCadreHR');
Route::POST('/datatableMyNotifications', 'NotificationController@datatableMyNotifications')->name('api.datatableMyNotifications');

/* Equipment start */
Route::POST('/datatableShowLaptop', 'EquipmentsController@datatableShowLaptop')->name('api.datatableShowLaptop');
Route::POST('/datatableShowTablet', 'EquipmentsController@datatableShowTablet')->name('api.datatableShowTablet');
Route::POST('/datatableShowPhone', 'EquipmentsController@datatableShowPhone')->name('api.datatableShowPhone');
Route::POST('/datatableShowSimCard', 'EquipmentsController@datatableShowSimCard')->name('api.datatableShowSimCard');
Route::POST('/datatableShowMonitor', 'EquipmentsController@datatableShowMonitor')->name('api.datatableShowMonitor');
Route::POST('/datatableShowPrinter', 'EquipmentsController@datatableShowPrinter')->name('api.datatableShowPrinter');
/* Equipment stop */

Route::POST('/datatableShowUserSchedule', 'ScheduleController@datatableShowUserSchedule')->name('api.datatableShowUserSchedule');
Route::POST('/saveSchedule', 'ScheduleController@saveSchedule')->name('api.saveSchedule');


Route::POST('/saveSummaryPayment', 'FinancesController@saveSummaryPayment')->name('api.summary_payment_save');
Route::POST('/editPenaltyBonus', 'FinancesController@editPenaltyBonus')->name('api.editPenaltyBonus');

Route::POST('/deletePenaltyBonus', 'FinancesController@deletePenaltyBonus')->name('api.deletePenaltyBonus');

Route::POST('/getUserDepartmentInfo', 'DkjController@getUserDepartmentInfo')->name('api.getUserDepartmentInfo');

Route::POST('/deleteMedicalPackage', 'UsersController@deleteMedicalPackage')->name('api.deleteMedicalPackage');

Route::POST('/editInterviewDateTime', 'CandidateController@editInterviewDateTime')->name('api.editInterviewDateTime');

Route::POST('/get_stats', 'DkjController@getStats')->name('api.getStats');
Route::POST('/get_stats_dkj', 'DkjController@getStatsDkjMaster')->name('api.getStatsDkj');
Route::POST('/get_users', 'DkjController@getUsers')->name('api.getUsers');


Route::POST('/change_department', 'HomeController@changeDepartment')->name('api.changeDepartment');

Route::POST('/it_support', 'HomeController@itSupport')->name('api.itSupport');
Route::POST('/count_notifications', 'HomeController@itCountNotifications')->name('api.itCountNotifications');
Route::POST('/datatableShowNewNotifications', 'NotificationController@datatableShowNewNotifications')->name('api.datatableShowNewNotifications'); //tu zmienic z ORM
Route::POST('/datatableShowInProgressNotifications', 'NotificationController@datatableShowInProgressNotifications')->name('api.datatableShowInProgressNotifications'); //tu zmienic z ORM
Route::POST('/datatableShowFinishedNotifications', 'NotificationController@datatableShowFinishedNotifications')->name('api.datatableShowFinishedNotifications'); //tu zmienic z ORM

//locker / Multiple departments
Route::POST('/locker', 'AdminController@lockerPost')->name('api.locker');

//firewall delete users
Route::POST('/firewallDeleteUser', 'AdminController@firewallDeleteUser')->name('api.firewallDeleteUser');

//notifications moving
Route::POST('/getNotficationsJanky', 'NotificationController@getNotficationsJanky')->name('api.getNotficationsJanky');

/* TEST AJAX ROUTES START */

Route::POST('/addTestQuestion', 'TestsController@addTestQuestion')->name('api.addTestQuestion');
Route::POST('/saveCategoryName', 'TestsController@saveCategoryName')->name('api.saveCategoryName');
Route::POST('/categoryStatusChange', 'TestsController@categoryStatusChange')->name('api.categoryStatusChange');
Route::POST('/showCategoryQuestions', 'TestsController@showCategoryQuestions')->name('api.showCategoryQuestions');

Route::POST('/editTestQuestion', 'TestsController@editTestQuestion')->name('api.editTestQuestion');
Route::POST('/deleteTestQuestion', 'TestsController@deleteTestQuestion')->name('api.deleteTestQuestion');
Route::POST('/mainTableCounter', 'TestsController@mainTableCounter')->name('api.mainTableCounter');

Route::POST('/showQuestionDatatable', 'TestsController@showQuestionDatatable')->name('api.showQuestionDatatable');
Route::POST('/saveTestWithUser', 'TestsController@saveTestWithUser')->name('api.saveTestWithUser');
Route::POST('/editTestWithUser', 'TestsController@editTestWithUser')->name('api.editTestWithUser');

Route::POST('/datatableShowCheckedTests', 'TestsController@datatableShowCheckedTests')->name('api.datatableShowCheckedTests');
Route::POST('/datatableShowUncheckedTests', 'TestsController@datatableShowUncheckedTests')->name('api.datatableShowUncheckedTests');

Route::POST('/activateTest', 'TestsController@activateTest')->name('api.activateTest');

Route::POST('/deactivateTest', 'TestsController@deactivateTest')->name('api.deactivateTest');

Route::POST('/testAttempt', 'TestsController@testAttempt')->name('api.testAttempt');

Route::POST('/getRepeatQuestion', 'TestsController@getRepeatQuestion')->name('api.getRepeatQuestion'); //tu zmienic z ORM
Route::POST('/saveTestTemplate', 'TestsController@saveTestTemplate')->name('api.saveTestTemplate'); //tu zmienic z ORM

Route::POST('/getTemplateQuestion', 'TestsController@getTemplateQuestion')->name('api.getTemplateQuestion'); //tu zmienic z ORM

Route::POST('/saveEditTemplate', 'TestsController@saveEditTemplate')->name('api.saveEditTemplate');

Route::POST('/deleteTester', 'TestsController@deleteTester')->name('api.deleteTester');

Route::POST('/datatableAllTests', 'AdminController@datatableAllTests')->name('api.datatableAllTests');

Route::POST('/paymentStory', 'FinancesController@paymentStory')->name('api.paymentStory');

Route::POST('/saveCoaching', 'CoachingController@saveCoaching')->name('api.saveCoaching');
Route::POST('/deleteCoaching', 'CoachingController@deleteCoaching')->name('api.deleteCoaching');
Route::POST('/getCoaching', 'CoachingController@getCoaching')->name('api.getCoaching');
Route::POST('/getcoach_list', 'CoachingController@getcoach_list')->name('api.getcoach_list');


// Dla Dyrektora
Route::POST('/saveCoachingDirector', 'CoachingController@saveCoachingDirector')->name('api.saveCoachingDirector');
Route::POST('/datatableCoachingTableDirector', 'CoachingController@datatableCoachingTableDirector')->name('api.datatableCoachingTableDirector');
Route::POST('/deleteCoachingTableDirector', 'CoachingController@deleteCoachingTableDirector')->name('api.deleteCoachingTableDirector');
Route::POST('/getCoachingDirector', 'CoachingController@getCoachingDirector')->name('api.getCoachingDirector');
Route::POST('/acceptCoachingDirector', 'CoachingController@acceptCoachingDirector')->name('api.acceptCoachingDirector');
Route::POST('/endCoachingDirector', 'CoachingController@endCoachingDirector')->name('api.endCoachingDirector');

Route::POST('/getManagerId', 'CoachingController@getManagerId')->name('api.getManagerId');


/* TEST AJAX ROUTES STOP */

//** RECRUITMENT AJAX */


Route::POST('/deleteGroupTraining', 'GroupTrainingController@deleteGroupTraining')->name('api.deleteGroupTraining');
Route::POST('/saveGroupTraining', 'GroupTrainingController@saveGroupTraining')->name('api.saveGroupTraining');
Route::POST('/EndGroupTraining', 'GroupTrainingController@EndGroupTraining')->name('api.EndGroupTraining');
Route::POST('/EndGroupTrainingForCandidate', 'GroupTrainingController@EndGroupTrainingForCandidate')->name('api.EndGroupTrainingForCandidate');

Route::post('editTrainingDate', 'GroupTrainingController@editTrainingDate')->name('api.editTrainingDate');

Route::POST('/getCandidateForGroupTrainingInfo', 'GroupTrainingController@getCandidateForGroupTrainingInfo')->name('api.getCandidateForGroupTrainingInfo');
Route::POST('/datatableTrainingGroupList', 'GroupTrainingController@datatableTrainingGroupList')->name('api.datatableTrainingGroupList');
Route::POST('/getGroupTrainingInfo', 'GroupTrainingController@getGroupTrainingInfo')->name('api.getGroupTrainingInfo');

Route::POST('/datatableRecruitmentStatisticsLeader', 'RecruitmentAttemptController@datatableRecruitmentStatisticsLeader')->name('api.datatableRecruitmentStatisticsLeader');


Route::POST('/getCandidateSource', 'RecruitmentAttemptController@getCandidateSource')->name('api.getCandidateSource');
Route::POST('/addCandidateSource', 'RecruitmentAttemptController@addCandidateSource')->name('api.addCandidateSource');
Route::POST('/editCandidateSource', 'RecruitmentAttemptController@editCandidateSource')->name('api.editCandidateSource');
Route::POST('/deleteCandidateSource', 'RecruitmentAttemptController@deleteCandidateSource')->name('api.deleteCandidateSource');

Route::POST('/addNewCandidate', 'CandidateController@addNewCandidate')->name('api.addNewCandidate');
Route::POST('/editCandidate', 'CandidateController@editCandidate')->name('api.editCandidate');
Route::POST('/addInterviewDate', 'RecruitmentAttemptController@addInterviewDate')->name('api.addInterviewDate');

Route::POST('/startNewRecruitment', 'CandidateController@startNewRecruitment')->name('api.startNewRecruitment');
Route::POST('/stopRecruitment', 'CandidateController@stopRecruitment')->name('api.stopRecruitment');
Route::POST('/addRecruitmentLevel', 'CandidateController@addRecruitmentLevel')->name('api.addRecruitmentLevel');
Route::POST('/addToTraining', 'CandidateController@addToTraining')->name('api.addToTraining');

Route::POST('/addConsultantToSession', 'CandidateController@addConsultantToSession')->name('api.addConsultantToSession');

Route::POST('/uniqueCandidatePhone', 'CandidateController@uniqueCandidatePhone')->name('api.uniqueCandidatePhone');

Route::POST('/datatableShowCandidates', 'CandidateController@datatableShowCandidates')->name('api.datatableShowCandidates');
Route::POST('/datatableShowCadreCandidates', 'CandidateController@datatableShowCadreCandidates')->name('api.datatableShowCadreCandidates');

Route::POST('/myInterviews', 'RecruitmentAttemptController@myInterviews')->name('api.myInterviews');

Route::POST('/getStatusResults', 'RecruitmentAttemptController@getStatusResults')->name('api.getStatusResults');
Route::POST('/statusResultChange', 'RecruitmentAttemptController@statusResultChange')->name('api.statusResultChange');

Route::POST('/recruiterData', 'RecruitmentAttemptController@recruiterData')->name('api.recruiterData');
Route::POST('/trainerData', 'RecruitmentAttemptController@trainerData')->name('api.trainerData');
Route::POST('/recruiterTrainingsData', 'RecruitmentAttemptController@recruiterTrainingsData')->name('api.recruiterTrainingsData');
Route::POST('/datatableTrainingData', 'RecruitmentStoryController@datatableTrainingData')->name('api.datatableTrainingData');

Route::POST('/delete_notification', 'NotificationController@delete_notification')->name('api.delete_notification');

Route::POST('/getMedicalPackagesAdminData', 'AdminController@getMedicalPackagesAdminData')->name('api.getMedicalPackagesAdminData');
Route::POST('/getMedicalPackageData', 'AdminController@getMedicalPackageData')->name('api.getMedicalPackageData');
Route::POST('/saveMedicalPackageData', 'AdminController@saveMedicalPackageData')->name('api.saveMedicalPackageData');
Route::POST('/logInfoDatatable', 'AdminController@datatableLogInfoAjax')->name('api.datatableLogInfo');


Route::POST('/getDaysInMonth', 'StatisticsController@getDaysInMonth')->name('api.getDaysInMonth');


Route::POST('/datatableCoachingTable', 'CoachingController@datatableCoachingTable')->name('api.datatableCoachingTable');


//Route::POST('/datatableCoachingTableDirector','CoachingController@datatableCoachingTableDirector')->name('api.datatableCoachingTableDirector');

Route::POST('/acceptCoaching', 'CoachingController@acceptCoaching')->name('api.acceptCoaching');;
//Bootstrap notifications //

Route::post('getBootstrapNotifications', 'HomeController@getBootstrapNotifications')->name('getBootstrapNotifications');

//bootstrap notifications end //

/* CRM-ROUTES AJAX */
Route::post('/crmRoute_index_ajax', 'CrmRouteController@getSelectedRoute')->name('api.getRoute');
Route::post('/getReadyRoute', 'CrmRouteController@getReadyRoute')->name('api.getReadyRoute');
Route::post('/updateClientRouteInfoHotelsAndHours', 'CrmRouteController@updateClientRouteInfoHotelsAndHours')->name('api.updateClientRouteInfoHotelsAndHours');
Route::post('/showClientRoutesAjax', 'CrmRouteController@showClientRoutesAjax')->name('api.getClientRoutes');
Route::post('/showClientRoutesInfoAjax', 'CrmRouteController@showClientRoutesInfoAjax')->name('api.getClientRouteInfo');
Route::post('/showClientRoutesStatus', 'CrmRouteController@showClientRoutesStatus')->name('api.showClientRoutesStatus');
Route::post('/getYearWeeksAjax', 'CrmRouteController@getYearWeeksAjax')->name('api.getWeeks');
Route::post('/addNewRoutes', 'CrmRouteController@addNewRouteAjax')->name('api.getCitiesNames');
Route::post('/showRoutesAjax', 'CrmRouteController@showRoutesAjax')->name('api.showRoutesAjax');
Route::post('/showHotelsAjax', 'CrmRouteController@showHotelsAjax')->name('api.showHotelsAjax');
Route::post('/getVoivodeshipRound', 'CrmRouteController@getVoivodeshipRound')->name('api.getVoivodeshipRound');
Route::POST('/changeStatusCity','CrmRouteController@changeStatusCity')->name('api.changeStatusCity');
Route::post('/getCity', 'CrmRouteController@getCity')->name('api.getCity');
Route::post('/findCity', 'CrmRouteController@findCity')->name('api.findCity');
Route::post('/findHotel', 'CrmRouteController@findHotel')->name('api.findHotel');
Route::POST('/saveNewCity','CrmRouteController@saveNewCity')->name('api.saveNewCity');
Route::POST('/saveNewHotel','CrmRouteController@saveNewHotel')->name('api.saveNewHotel');
Route::POST('/uploadHotelFilesAjax', 'CrmRouteController@uploadHotelFilesAjax')->name('api.uploadHotelFilesAjax');
Route::POST('/downloadHotelFilesAjax', 'CrmRouteController@downloadHotelFilesAjax')->name('api.downloadHotelFilesAjax');
Route::POST('/changeStatusHotel','CrmRouteController@changeStatusHotel')->name('api.changeStatusHotel');
Route::POST('/getCampaignsInvoicesDatatableAjax','CrmRouteController@getCampaignsInvoicesDatatableAjax')->name('api.getCampaignsInvoicesDatatableAjax');
Route::POST('/uploadCampaignInvoiceAjax','CrmRouteController@uploadCampaignInvoiceAjax')->name('api.uploadCampaignInvoiceAjax');
Route::POST('/getClientInfoAjax','CrmRouteController@getClientInfoAjax')->name('api.getClientInfoAjax');
Route::POST('/confirmPaymentAjax','CrmRouteController@confirmPaymentAjax')->name('api.confirmPaymentAjax');
Route::post('/getRouteTemplate', 'CrmRouteController@getRouteTemplate')->name('api.getRouteTemplate');
Route::post('/getVoivodeshipRoundWithDistanceLimit', 'CrmRouteController@getVoivodeshipRoundWithDistanceLimit')->name('api.getVoivodeshipRoundWithDistanceLimit');
Route::post('/getVoivodeshipRoundWithoutGracePeriodAjax', 'CrmRouteController@getVoivodeshipRoundWithoutGracePeriodAjax')->name('api.getVoivodeshipRoundWithoutGracePeriod');
Route::post('/allCitiesInGivenVoivodeAjax', 'CrmRouteController@allCitiesInGivenVoivodeAjax')->name('api.allCitiesInGivenVoivodeAjax');
Route::post('/deleteRouteTemplate', 'CrmRouteController@deleteRouteTemplate')->name('api.deleteRouteTemplate');

Route::post('/getConfirmHotelInfo','CrmRouteController@getConfirmHotelInfo')->name('api.getConfirmHotelInfo');
Route::post('/changeConfirmStatus','CrmRouteController@changeConfirmStatus')->name('api.changeConfirmStatus');


Route::POST('/clientReport', 'CrmRouteController@clientReport')->name('api.clientReport');
Route::post('/getClient', 'ClientController@getClient')->name('api.getClient');
Route::post('/getGiftType', 'ClientController@getGiftType')->name('api.getGiftType');
Route::post('/getMeetingType', 'ClientController@getMeetingType')->name('api.getMeetingType');

Route::post('/saveNewGift', 'ClientController@saveNewGift')->name('api.saveNewGift');
Route::post('/saveNewMeeting', 'ClientController@saveNewMeeting')->name('api.saveNewMeeting');

Route::post('/changeGiftStatus', 'ClientController@changeGiftStatus')->name('api.changeGiftStatus');
Route::post('/changeMeetingStatus', 'ClientController@changeMeetingStatus')->name('api.changeMeetingStatus');

Route::post('/editGift', 'ClientController@editGift')->name('api.editGift');
Route::POST('/editMeeting','ClientController@editMeeting')->name('api.editMeeting');

Route::post('/findClient', 'ClientController@findClient')->name('api.findClient');
Route::POST('/changeStatusClient','ClientController@changeStatusClient')->name('api.changeStatusClient');

Route::POST('/saveClient','ClientController@saveClient')->name('api.saveClient');
//Route::POST('/EditClient','ClientController@EditClient')->name('api.saveClient');
Route::post('/showRoutesDetailedAjax', 'CrmRouteController@showRoutesDetailedAjax')->name('api.getDetailedInfo');
Route::post('/saveCampaignOption', 'CrmRouteController@saveCampaignOption')->name('api.saveCampaignOption');
Route::post('/showCitiesStatisticsAjax', 'CrmRouteController@showCitiesStatisticsAjax')->name('api.showCitiesStatisticsAjax');
Route::post('/campaignsInfo', 'CrmRouteController@campaignsInfo')->name('api.campaignsInfo');
Route::post('/showRoutesDetailedUpdateAjax', 'CrmRouteController@showRoutesDetailedUpdateAjax')->name('api.updateClientRouteInfoRecords');
Route::post('/getClientRouteInfoRecords', 'CrmRouteController@getClientRouteInfoRecords')->name('api.getClientRouteInfoRecords');
Route::post('/getaHeadPlanningInfo', 'CrmRouteController@getaHeadPlanningInfo')->name('api.getaHeadPlanningInfo');

Route::post('/sendMailWithInvoice', 'CrmRouteController@sendMailWithInvoice')->name('api.sendMailWithInvoice');

Route::get('/getPresentationInfo', 'CrmRouteController@getPresentationInfo')->name('api.getPresentationInfo');
Route::post('/saveCampaignOption', 'CrmRouteController@saveCampaignOption')->name('api.saveCampaignOption');
Route::post('/presentationStatisticsAjax', 'CrmRouteController@presentationStatisticsAjax')->name('getPresentationWeeks');
Route::post('/getClientRouteInfoRecord', 'CrmRouteController@getClientRouteInfoRecord')->name('api.getClientRouteInfoRecord');
Route::post('/datatableClientRouteInfoAjax', 'CrmRouteController@datatableClientRouteInfoAjax')->name('api.datatableClientRouteInfoAjax');
/* END CRM-ROUTES AJAX */

/* HR-ROUTES AJAX */
Route::post('/coachChange', 'UsersController@coachChangePost')->name('api.coachChange');
Route::post('/datatableCoachChange', 'UsersController@datatableCoachChange')->name('api.datatableCoachChange');
Route::post('/coachChangeRevert', 'UsersController@coachChangeRevertPost')->name('api.coachChangeRevert');
/* END HR-ROUTES AJAX */

/* COACHING ROUTES AJAX*/
Route::post('/revertSettlement', 'CoachingController@revertSettlementPost')->name('api.revertSettlement');
Route::post('/coachAscription', 'CoachingController@coachAscriptionPost')->name('api.coachAscription');
Route::post('/datatableCoachAscription', 'CoachingController@datatableCoachAscription')->name('api.datatableCoachAscription');
Route::post('/coachAscriptionRevert', 'CoachingController@coachAscriptionRevertPost')->name('api.coachAscriptionRevert');

/* END COACHING ROUTES AJAX*/


/* Report Database ROUTES AJAX*/
Route::post('/pbxReportDetailedAjax', 'StatisticsController@pbxReportDetailedAjax')->name('api.pbxReportDetailedAjax');
/* END Report Database AJAX*/




//********************END AJAX*********************** */

//Emails

Route::get('/hourReportTelemarketing', 'StatisticsController@MailhourReportTelemarketing'); // ok
Route::get('/weekReportTelemarketing', 'StatisticsController@MailweekReportTelemarketing'); // ok
Route::get('/monthReportTelemarketing', 'StatisticsController@MailmonthReportTelemarketing'); // ok
Route::get('/dayReportTelemarketing', 'StatisticsController@MailDayReportTelemarketing'); // tutaj dodac

Route::get('/weekReportPlanningRBH', 'StatisticsRBHController@WeekReportPlanningRBHMail');
Route::get('/dayReport30RBH', 'StatisticsRBHController@WeekReportPlanningRBHMail');

Route::get('/invoices', 'CrmRouteController@invoicesMail');
//Gniezno
Route::get('/hourReportTelemarketingGniezno', 'OtherCompanyStatisticsController@MailhourReportTelemarketing'); // ok
Route::get('/weekReportTelemarketingGniezno', 'OtherCompanyStatisticsController@MailweekReportTelemarketing'); // ok
Route::get('/monthReportTelemarketingGnhaiezno', 'OtherCompanyStatisticsController@MailmonthReportTelemarketing'); // ok
Route::get('/dayReportTelemarketingGniezno', 'OtherCompanyStatisticsController@MailDayReportTelemarketing'); // tutaj dodac


Route::get('/weekReportJanky', 'StatisticsController@MailweekReportJanky');
Route::get('/dayReportMissedRepo', 'StatisticsController@dayReportMissedRepo');

Route::get('/hourReportDkj', 'StatisticsController@MailhourReportDkj'); // ok
Route::get('/dayReportDkj', 'StatisticsController@dayReportDkj');// ok
Route::get('/weekReportDkj', 'StatisticsController@MailWeekReportDkj');// ok
Route::get('/monthReportDkj', 'StatisticsController@monthReportDkj');// ok

//Gniezno
Route::get('/hourReportDkjGniezno', 'OtherCompanyStatisticsController@MailhourReportDkj'); // ok
Route::get('/dayReportDkjGniezno', 'OtherCompanyStatisticsController@dayReportDkj');// ok
Route::get('/weekReportDkjGniezno', 'OtherCompanyStatisticsController@MailWeekReportDkj');// ok
Route::get('/monthReportDkjGniezno', 'OtherCompanyStatisticsController@monthReportDkj');// ok

Route::get('/dayReportEmployeeDkj', 'StatisticsController@MaildayReportEmployeeDkj');
Route::get('/hourReportDkjEmployee', 'StatisticsController@MailHourReportDkjEmployee');
Route::get('/weekReportEmployeeDkj', 'StatisticsController@MailweekReportEmployeeDkj');

Route::get('/hourReportChecked', 'StatisticsController@hourReportChecked');
Route::get('/dayReportChecked', 'StatisticsController@dayReportChecked');
Route::get('/weekReportChecked', 'StatisticsController@weekReportChecked');

Route::get('/hourReportTimeOnRecord', 'StatisticsController@MailhourReportTimeOnRecord');

// maile dotyczące bazy danych (telefonów) maile

Route::get('/weekReportNewBaseWeek', 'DatabaseURLController@MailWeekRaportNewBaseWeek');
Route::get('/monthReportNewBaseWeek', 'DatabaseURLController@MailMonthRaportNewBaseWeek');

Route::get('/dayReportDatabaseUse', 'DatabaseURLController@MailDayRaportDatabaseUse');
Route::get('/weekReportDatabaseUse', 'DatabaseURLController@MailWeekRaportDatabaseUse');
Route::get('/monthReportDatabaseUse', 'DatabaseURLController@MailMonthRaportDatabaseUse');
//End emails

// maila dotyczące rekrutacji
Route::get('/dayReportRecruitmentFlow', 'StatisticsController@MaildayReportRecruitmentFlow');
Route::get('/dayReportRecruitmentTrainingGroup', 'StatisticsController@MaildayReportTrainingGroup');
Route::get('/dayReportInterviews', 'StatisticsController@MaildayReportInterviews');
Route::get('/dayReportHireCandidate', 'StatisticsController@MaildayReportHireCandidate');

Route::get('/weekReportRecruitmentFlow', 'StatisticsController@MailweekReportRecruitmentFlow');
Route::get('/weekReportTrainingGroup', 'StatisticsController@MailweekReportTrainingGroup');
Route::get('/weekReportInterviews', 'StatisticsController@MailweekReportInterviews');
Route::get('/weekReportHireCandidate', 'StatisticsController@MailweekReportHireCandidate');

Route::get('/monthReportRecruitmentFlow', 'StatisticsController@MailmonthReportRecruitmentFlow');
Route::get('/monthReportTrainingGroup', 'StatisticsController@MailmonthReportTrainingGroup');
Route::get('/monthReportInterviews', 'StatisticsController@MailmonthReportInterviews');
Route::get('/monthReportHireCandidate', 'StatisticsController@MailmonthReportHireCandidate');

//Emaile dotyczące statystyk oddziałów
Route::get('/monthReportSummaryDepartments', 'StatisticsController@MailMonthReportDepartments');

Route::get('/weekReportDepartmentsRanking', 'StatisticsController@WeekReportDepartmentsRanking');

Route::get('/dayReportDepartments', 'StatisticsController@MailDayDepartmentsReport');
Route::get('/dayReportCoaches', 'StatisticsController@MailDayReportCoaches');
Route::get('/hourReportCoaches', 'StatisticsController@MailHourReportCoaches');

//Emaile dotyczące raportów kampani
Route::get('/mailDayReportCampaign', 'StatisticsController@mailDayReportCampaign');
Route::get('/mailWeekReportCampaign', 'StatisticsController@mailWeekReportCampaign');
Route::get('/mailMonthReportCampaign', 'StatisticsController@mailMonthReportCampaign');



//Maila dotyczące wyłączonych kont
//Raport Usunietych kont
Route::get('/weekReportUnuserdAccount', 'StatisticsController@MailWeekReportUnuserdAccount');

//wyłączenie danych użytkowników którzy nie logowali się już 14 dni i więcej
Route::GET('/disableUnusedAccount', 'UsersController@DisableUnusedAccount');


Auth::routes();
//'check-firewall'
Route::middleware(['check-permission', 'check-firewall'])->group(function () {
    Route::get('/', 'HomeController@index');
    // Admin_Panel --Start--
    Route::get('/admin_privilage', 'AdminController@admin_privilage');

    Route::get('/admin_privilage_show/{id}', 'AdminController@admin_privilage_show');
    Route::Post('/admin_privilage_edit/{id}', 'AdminController@admin_privilage_edit');

    Route::get('/locker', 'AdminController@lockerGet');

    Route::get('/add_department', 'AdminController@addDepartmentGet');
    Route::Post('/add_department', 'AdminController@addDepartmentPost');

    Route::get('/edit_department', 'AdminController@editDepartmentGet');
    Route::Post('/edit_department', 'AdminController@editDepartmentPost');

    Route::get('/set_multiple_department', 'AdminController@multipleDepartmentGet');
    Route::Post('/set_multiple_department', 'AdminController@multipleDepartmentPost');

    Route::get('/create_link', 'AdminController@createLinkGet');
    Route::Post('/create_link', 'AdminController@createLinkPost');

    Route::Post('/addGroup', 'AdminController@addGroup');
    Route::Post('/removeGroup', 'AdminController@removeGroup');

    Route::get('/firewall_ip', 'AdminController@firewallGet');
    Route::POST('/firewall_ip', 'AdminController@firewallPost');

    Route::get('/firewall_privileges', 'AdminController@firewallPrivilegesGet');
    Route::POST('/firewall_privileges', 'AdminController@firewallPrivilegesPost');

    Route::get('/check_all_tests', 'AdminController@check_all_tests');

    Route::get('/show_test_for_admin/{id}', 'AdminController@show_test_for_admin');

    Route::get('/edit_medical_package', 'AdminController@edit_medical_package');

    Route::get('/logInfo', 'AdminController@logInfoGet');
    // Admin_Panel --Stop--

    // Password change --START--

    Route::get('/password_change', 'UsersController@passwordChangeGet')->name('changePassword');
    Route::Post('/password_change', 'UsersController@passwordChangePost');

    // Password change --STOP--
    // Work_hours --Start--
    Route::get('/accept_hour', 'WorkHoursController@acceptHour');
    Route::get('/add_hour', 'WorkHoursController@addHour');
    Route::get('/view_hour', 'WorkHoursController@viewHourGet');
    Route::Post('/view_hour', 'WorkHoursController@viewHourPost');

    Route::get('/accept_hour_cadre', 'WorkHoursController@acceptHourCadre');

    Route::get('/view_hour_cadre', 'WorkHoursController@viewHourGetCadre');
    Route::Post('/view_hour_cadre', 'WorkHoursController@viewHourPostCadre');

    Route::get('/check_list_cadre', 'WorkHoursController@checkListCadre');

    Route::get('/users_live', 'WorkHoursController@usersLive');
    // Work_hours --end--

    // Users --Start--
    Route::get('/add_consultant', 'UsersController@add_consultantGet');
    Route::POST('/add_consultant', 'UsersController@add_userPOST');

    //Changing coach groups
    Route::get('/coachChange', 'UsersController@coachChangeGet');

    Route::get('/edit_consultant/{id}', 'UsersController@edit_consultantGet');
    //Route::POST('/edit_consultant/{id}','UsersController@edit_consultantPOST');

    Route::get('/edit_cadre/{id}', 'UsersController@edit_cadreGet');
    Route::POST('/edit_cadre/{id}', 'UsersController@edit_cadrePOST');

    Route::get('/employee_management', 'UsersController@employee_managementGet');
    Route::post('/employeeSearch', 'UsersController@employeeSearchPost');

    Route::get('/cadre_management', 'UsersController@cadre_managementGet');
    Route::get('/cadre_management_fire', 'UsersController@cadre_management_fireGet');

    Route::get('/add_cadre', 'UsersController@add_cadreGet');
    Route::POST('/add_cadre', 'UsersController@add_userPOST');

    Route::get('/cadre_hr', 'UsersController@cadreHRGet');

    Route::get('/medical_packages_all', 'UsersController@medicalPackagesAllGet');
    Route::POST('/medical_packages_all', 'UsersController@medicalPackagesAllPost');
    // Users -- STOP--


    // DKJ --START--
    Route::get('/dkjRaport', 'DkjController@dkjRaportGet');
    Route::POST('/dkjRaport', 'DkjController@dkjRaportPOST');

    Route::get('/dkjVerification', 'DkjController@dkjVerificationGet');

    Route::get('/jankyVerification', 'DkjController@jankyVerificationGet');
    Route::POST('/jankyVerification', 'DkjController@jankyVerificationPOST');

    Route::get('/jankyStatistics', 'DkjController@jankyStatistics');

    Route::get('/departmentStatistics', 'DkjController@departmentStatisticsGet');
    Route::POST('/departmentStatistics', 'DkjController@departmentStatisticsPOST');

    Route::get('/departmentsStatistics', 'DkjController@departmentsStatisticsGet');
    Route::POST('/departmentsStatistics', 'DkjController@departmentsStatisticsPOST');

    Route::get('/consultantStatistics', 'DkjController@consultantStatisticsGet');
    Route::POST('/consultantStatistics', 'DkjController@consultantStatisticsPOST');

    Route::get('/showDkjEmployee', 'DkjController@showDkjEmployeeGet');
    Route::POST('/showDkjEmployee', 'DkjController@showDkjEmployeePOST');
    // DKJ -- STOP--

    // Schedule -- START --
    Route::get('/set_schedule', 'ScheduleController@setScheduleGet');
    Route::Post('/set_schedule', 'ScheduleController@setSchedulePOST');

    Route::get('/view_schedule', 'ScheduleController@viewScheduleGet');
    Route::Post('/view_schedule', 'ScheduleController@viewSchedulePOST');


    Route::get('/timesheet', 'ScheduleController@timesheetGet');
    Route::POST('/timesheet', 'ScheduleController@timesheetPost');

    Route::get('/timesheet_cadre', 'ScheduleController@timesheetCadreGet');
    Route::POST('/timesheet_cadre', 'ScheduleController@timesheetCadrePost');
    // schedule -- STOP --

    // Finances -- START --
    Route::get('/view_payment', 'FinancesController@viewPaymentGet');
    Route::Post('/view_payment', 'FinancesController@viewPaymentPOST');

    Route::get('/view_payment_cadre', 'FinancesController@viewPaymentCadreGet');
    Route::Post('/view_payment_cadre', 'FinancesController@viewPaymentCadrePOST');


    Route::get('/view_penalty_bonus', 'FinancesController@viewPenaltyBonusGet');
    Route::Post('/view_penalty_bonus', 'FinancesController@viewPenaltyBonusPOST');

    Route::Post('/create_penalty_bonus', 'FinancesController@createPenaltyBonusPOST');

    Route::Post('/view_penalty_bonus_edit', 'FinancesController@viewPenaltyBonusPostEdit');

    Route::get('/view_summary_payment', 'FinancesController@viewSummaryPaymentGet');
    Route::Post('/view_summary_payment', 'FinancesController@viewSummaryPaymentPOST');

    Route::get('/medicalPackagesRaportExtended', 'UsersController@medicalPackagesRaportExtendedGet');
    Route::post('/medicalPackagesRaportExtended', 'UsersController@medicalPackagesRaportExtendedPost');
    // Finances -- STOP --

    // Equipment -- START --
    Route::get('/show_equipment', 'EquipmentsController@showEquipment');

    Route::get('/edit_equipment/{id}', 'EquipmentsController@editEquipmentGet');
    Route::Post('/edit_equipment/{id}', 'EquipmentsController@editEquipmentPost');

    Route::get('/add_equipment/{type}', 'EquipmentsController@addEquipmentGet');
    Route::Post('/add_equipment', 'EquipmentsController@addEquipmentPost');
    // Equipment -- STOP --

    //Notification Start
    Route::get('/add_notification', 'NotificationController@addNotificationGet');
    Route::Post('/add_notification', 'NotificationController@addNotificationPost');

    Route::get('/show_notification/{id}', 'NotificationController@showNotificationGet');
    Route::Post('/show_notification/{id}', 'NotificationController@showNotificationPost');

    Route::Post('add_comment_notifications/{id}', 'NotificationController@addCommentNotificationPost');

    Route::get('/show_all_notifications', 'NotificationController@showAllNotificationsGet');

    Route::get('/my_notifications', 'NotificationController@myNotifications');

    Route::get('/judge_notification/{id}', 'NotificationController@judgeNotificationGet');
    Route::Post('/judge_notification', 'NotificationController@judgeNotificationPost');

    Route::get('/it_cadre', 'NotificationController@ITCadreGet');

    Route::get('/it_worker/{id}', 'NotificationController@ITWorkerGet');

    Route::get('/view_notification/{id}', 'NotificationController@viewNotification');
    //Notification STOP


    //Statistics Start
    Route::get('/hour_report', 'StatisticsController@hourReportGet');
    Route::Post('/hour_report', 'StatisticsController@hourReportPost');

    Route::Post('/hour_report_edit', 'StatisticsController@hourReportEditPost');

    Route::get('/pageReportDepartments', 'StatisticsController@pageReportDepartmentsGet');
    Route::post('/pageReportDepartments', 'StatisticsController@pageReportDepartmentsPost');
    //Statistics Stop

    //Report Page Start
    Route::get('/pageDayReportMissedRepo', 'StatisticsController@dayReportMissedRepo');

    //Campaign reports
    Route::get('/dayReportCampaign', 'StatisticsController@dayReportCampaignGet');
    Route::post('/dayReportCampaign', 'StatisticsController@dayReportCampaignPost');

    //PlanningRBH Rerpot
    Route::get('/pageWeekReportPlanningRBH', 'StatisticsRBHController@weekReportPlanningRBHGet');
    Route::post('/pageWeekReportPlanningRBH', 'StatisticsRBHController@weekReportPlanningRBHPost');
    //New Users RBH Rerpot
    Route::get('/pageDayReport30RBH', 'StatisticsRBHController@dayReport30RBHGet');
    Route::Post('/pageDayReport30RBH', 'StatisticsRBHController@dayReport30RBHPost');

    Route::get('/weekReportCampaign', 'StatisticsController@weekReportCampaignGet');
    Route::post('/weekReportCampaign', 'StatisticsController@weekReportCampaignPost');

    Route::get('/monthReportCampaign', 'StatisticsController@monthReportCampaignGet');
    Route::post('/monthReportCampaign', 'StatisticsController@monthReportCampaignPost');


    Route::get('/pageHourReportTelemarketing', 'StatisticsController@pageHourReportTelemarketing');
    Route::get('/pageWeekReportTelemarketing', 'StatisticsController@pageWeekReportTelemarketing');
    Route::post('/pageWeekReportTelemarketing', 'StatisticsController@pageWeekReportTelemarketingPost');
    Route::get('/pageMonthReportTelemarketing', 'StatisticsController@pageMonthReportTelemarketing');
    Route::get('/pageDayReportTelemarketing', 'StatisticsController@pageDayReportTelemarketing');
    Route::post('/pageDayReportTelemarketing', 'StatisticsController@pageDayReportTelemarketingPost');


    //Gniezno Telemarketing
    Route::get('/pageHourReportTelemarketingGniezno', 'OtherCompanyStatisticsController@pageHourReportTelemarketing');
    Route::get('/pageWeekReportTelemarketingGniezno', 'OtherCompanyStatisticsController@pageWeekReportTelemarketing');
    Route::get('/pageMonthReportTelemarketingGniezno', 'OtherCompanyStatisticsController@pageMonthReportTelemarketing');
    Route::get('/pageDayReportTelemarketingGniezno', 'OtherCompanyStatisticsController@pageDayReportTelemarketing');
    //Gniezno DKJ
    Route::get('/pageHourReportDKJGniezno', 'OtherCompanyStatisticsController@pageHourReportDKJ');
    Route::get('/pageDayReportDKJGniezno', 'OtherCompanyStatisticsController@pageDayReportDKJ');
    Route::get('/pageWeekReportDKJGniezno', 'OtherCompanyStatisticsController@pageWeekReportDKJ');
    Route::get('/pageMonthReportDKJGniezno', 'OtherCompanyStatisticsController@pageMonthReportDKJ');
    //Gniezno   END

    Route::get('/pageHourReportDKJ', 'StatisticsController@pageHourReportDKJ');
    Route::get('/pageDayReportDKJ', 'StatisticsController@pageDayReportDKJ');
    Route::post('/pageDayReportDKJ', 'StatisticsController@pageDayReportDKJPost');
    Route::get('/pageWeekReportDKJ', 'StatisticsController@pageWeekReportDKJ');
    Route::post('/pageWeekReportDKJ', 'StatisticsController@pageWeekReportDKJPost');
    Route::get('/pageMonthReportDKJ', 'StatisticsController@pageMonthReportDKJ');
    Route::post('/pageMonthReportDKJ', 'StatisticsController@pageMonthReportDKJPost');

    Route::get('/pageDayReportEmployeeDkj', 'StatisticsController@pageDayReportEmployeeDkj');
    Route::post('/pageDayReportEmployeeDkj', 'StatisticsController@pageDayReportEmployeeDkjPost');

    Route::get('/pageWeekReportEmployeeDkj', 'StatisticsController@pageWeekReportEmployeeDkj');
    Route::post('/pageWeekReportEmployeeDkj', 'StatisticsController@pageWeekReportEmployeeDkjPost');

    Route::get('/pageHourReportChecked', 'StatisticsController@pageHourReportChecked');
    Route::get('/pageDayReportChecked', 'StatisticsController@pageDayReportChecked');
    Route::post('/pageDayReportChecked', 'StatisticsController@pageDayReportCheckedPost');
    Route::get('/pageWeekReportChecked', 'StatisticsController@pageWeekReportChecked');
    Route::post('/pageWeekReportChecked', 'StatisticsController@pageWeekReportCheckedPost');

    Route::get('/pageWeekReportJanky', 'StatisticsController@pageWeekReportJanky');
    Route::post('/pageWeekReportJanky', 'StatisticsController@pageWeekReportJankyPost');

    Route::get('/pageHourReportDkjEmployee', 'StatisticsController@pageHourReportDkjEmployee');

    Route::get('/pageWeekReportNewBaseWeek', 'DatabaseURLController@pageWeekRaportNewBaseWeek');
    Route::get('/pageMonthReportNewBaseWeek', 'DatabaseURLController@pageMonthRaportNewBaseWeek');

    Route::get('/pageDayReportDatabaseUse', 'DatabaseURLController@pageDayRaportDatabaseUse');
    Route::get('/pageWeekReportDatabaseUse', 'DatabaseURLController@pageWeekRaportDatabaseUse');
    Route::get('/pageMonthReportDatabaseUse', 'DatabaseURLController@pageMonthRaportDatabaseUse');

    Route::get('/pbxReportDetailed', 'StatisticsController@pbxReportDetailedGet');

    Route::get('/pageHourReportTimeOnRecord', 'StatisticsController@pageHourReportTimeOnRecord');

    Route::get('/pageMailMonthReportDepartments', 'StatisticsController@pageMailMonthReportDepartments');

    Route::get('/pageMonthReportCoach', 'StatisticsController@pageMonthReportCoachGet');
    Route::post('/pageMonthReportCoach', 'StatisticsController@pageMonthReportCoachPost');
    Route::get('/pageDayReportCoaches', 'StatisticsController@pageDayReportCoachGet');
    Route::post('/pageDayReportCoaches', 'StatisticsController@pageDayReportCoachPost');
    Route::get('/pageSummaryDayReportCoaches', 'StatisticsController@pageSummaryDayReportCoachesGet');
    Route::post('/pageSummaryDayReportCoaches', 'StatisticsController@pageSummaryDayReportCoachesPost');

    Route::get('/pageMonthReportDepartmentsSummary', 'StatisticsController@pageMonthReportDepartmentsSummaryGet');
    Route::post('/pageMonthReportDepartmentsSummary', 'StatisticsController@pageMonthReportDepartmentsSummaryPost');

    Route::get('/pageWeekReportDepartmentsRanking', 'StatisticsController@pageWeekReportDepartmentsRankingGet');
    Route::POST('/pageWeekReportDepartmentsRanking', 'StatisticsController@pageWeekReportDepartmentsRankingPost');

    Route::get('/pageWeekReportDepartmentsSummary', 'StatisticsController@pageWeekReportDepartmentsSummaryGet');
    Route::post('/pageWeekReportDepartmentsSummary', 'StatisticsController@pageWeekReportDepartmentsSummaryPost');

    Route::get('/pageMonthReportCoachSummary', 'StatisticsController@pageMonthReportCoachSummaryGet');
    Route::post('/pageMonthReportCoachSummary', 'StatisticsController@pageMonthReportCoachSummaryPost');

    Route::get('/monthReportConsultant', 'StatisticsController@monthReportConsultantGet');
    Route::post('/monthReportConsultant', 'StatisticsController@monthReportConsultantPost');

    Route::get('/pageMonthReportCoachRanking', 'StatisticsController@pageMonthReportCoachRankingGet');
    Route::post('/pageMonthReportCoachRanking', 'StatisticsController@pageMonthReportCoachRankingPost');

    Route::get('/pageMonthReportCoachRankingOrderable', 'StatisticsController@pageMonthReportCoachRankingOrderableGet');
    Route::post('/pageMonthReportCoachRankingOrderable', 'StatisticsController@pageMonthReportCoachRankingOrderablePost');

    //Raporty Rekrutacji
    //Dzienny
    Route::get('/pageDayReportRecruitmentFlow', 'StatisticsController@pageDayReportRecruitmentFlow');
    Route::post('/pageDayReportRecruitmentFlow', 'StatisticsController@pageDayReportRecruitmentFlowPost');
    Route::get('/pageDayReportRecruitmentTrainingGroupFirstAndHire', 'StatisticsController@pageDayReportRecruitmentTrainingGroupFirstAndHireGET');
    Route::post('/pageDayReportRecruitmentTrainingGroupFirstAndHire', 'StatisticsController@pageDayReportRecruitmentTrainingGroupFirstAndHirePOST');
    Route::get('/pageDayReportRecruitmentTrainingGroup', 'StatisticsController@pageDayReportTrainingGroup');
    Route::post('/pageDayReportRecruitmentTrainingGroup', 'StatisticsController@pageDayReportTrainingGroupPost');
    Route::get('/pageDayReportInterviews', 'StatisticsController@pageDayReportInterviews');
    Route::post('/pageDayReportInterviews', 'StatisticsController@pageDayReportInterviewsPost');
    Route::get('/pageDayReportHireCandidate', 'StatisticsController@pageDayReportHireCandidate');
    Route::post('/pageDayReportHireCandidate', 'StatisticsController@pageDayReportHireCandidatePost');
    //Tygodniowy
    Route::get('/pageWeekReportRecruitmentFlow', 'StatisticsController@pageWeekReportRecruitmentFlow');
    Route::post('/pageWeekReportRecruitmentFlow', 'StatisticsController@pageWeekReportRecruitmentFlowPost');
    Route::get('/pageWeekReportTrainingGroup', 'StatisticsController@pageWeekReportTrainingGroup');
    Route::post('/pageWeekReportTrainingGroup', 'StatisticsController@pageWeekReportTrainingGroupPost');
    Route::get('/pageWeekReportInterviews', 'StatisticsController@pageWeekReportInterviews');
    Route::post('/pageWeekReportInterviews', 'StatisticsController@pageWeekReportInterviewsPost');
    Route::get('/pageWeekReportHireCandidate', 'StatisticsController@pageWeekReportHireCandidate');
    Route::post('/pageWeekReportHireCandidate', 'StatisticsController@pageWeekReportHireCandidatePost');
    Route::get('/pageWeekReportRecruitmentTrainingGroupFirstAndHire', 'StatisticsController@pageWeekReportRecruitmentTrainingGroupFirstAndHireGET');
    Route::post('/pageWeekReportRecruitmentTrainingGroupFirstAndHire', 'StatisticsController@pageWeekReportRecruitmentTrainingGroupFirstAndHirePOST');

    //Miesięczny
    Route::get('/pageMonthReportRecruitmentFlow', 'StatisticsController@pageMonthReportRecruitmentFlow');
    Route::post('/pageMonthReportRecruitmentFlow', 'StatisticsController@pageMonthReportRecruitmentFlowPost');
    Route::get('/pageMonthReportTrainingGroup', 'StatisticsController@pageMonthReportTrainingGroup');
    Route::post('/pageMonthReportTrainingGroup', 'StatisticsController@pageMonthReportTrainingGroupPost');
    Route::get('/pageMonthReportInterviews', 'StatisticsController@pageMonthReportInterviews');
    Route::post('/pageMonthReportInterviews', 'StatisticsController@pageMonthReportInterviewsPost');
    Route::get('/pageMonthReportHireCandidate', 'StatisticsController@pageMonthReportHireCandidate');
    Route::post('/pageMonthReportHireCandidate', 'StatisticsController@pageMonthReportHireCandidatePost');
    Route::get('/pageMonthReportRecruitmentTrainingGroupFirstAndHire', 'StatisticsController@pageMonthReportRecruitmentTrainingGroupFirstAndHireGET');
    Route::post('/pageMonthReportRecruitmentTrainingGroupFirstAndHire', 'StatisticsController@pageMonthReportRecruitmentTrainingGroupFirstAndHirePOST');


    //Raport Usunietych kont
    Route::get('/pageWeekReportUnuserdAccount', 'StatisticsController@pageWeekReportUnuserdAccount');
    //Report Page STOP

    //TESTS START //

    Route::get('/tests_admin_panel', 'TestsController@testsAdminPanelGet');
    Route::POST('/tests_admin_panel', 'TestsController@testsAdminPanelPost');

    Route::get('/tester_list', 'TestsController@testerListGet');
    Route::POST('/tester_list', 'TestsController@testerListPost');

    Route::get('/test_user/{id}', 'TestsController@testUserGet'); // tutaj bedzie {id}
    Route::POST('/test_user', 'TestsController@testUserPost');

    Route::get('/all_user_tests', 'TestsController@allUserTests');

    Route::get('/add_test', 'TestsController@addTestGet');
    Route::POST('/add_test', 'TestsController@addTestPost');
    Route::get('/view_test/{id}', 'TestsController@viewTest'); // podgląd testu
    Route::get('/delete_test/{id}', 'TestsController@deleteTest'); // usunięcie  testu
    Route::get('/show_tests', 'TestsController@showTestsGet');
    Route::POST('/show_tests', 'TestsController@showTestsPost');

    Route::get('/check_test/{id}', 'TestsController@testCheckGet'); // tutaj bedzie {id}
    Route::POST('/check_test', 'TestsController@testCheckPost');

    Route::get('/tests_statistics', 'TestsController@testsStatisticsGet');
    Route::POST('/tests_statistics', 'TestsController@testsStatisticsPost');

    Route::get('/tests_statistics_coach', 'TestsController@testsStatisticsCoachGet');
    Route::POST('/tests_statistics_coach', 'TestsController@testsStatisticsCoachPost');

    Route::get('/one_test_statistics', 'TestsController@testStatisticsGet');
    Route::POST('/one_test_statistics', 'TestsController@testStatisticsPost');

    Route::get('/employee_statistics/{id}', 'TestsController@employeeTestsStatisticsGet'); // tutaj bedzie {id}

    Route::get('/department_statistics', 'TestsController@departmentTestsStatisticsGet');
    Route::POST('/department_statistics', 'TestsController@departmentTestsStatisticsPost');

    Route::get('/test_result/{id}', 'TestsController@testResult');

    Route::get('/add_test_template', 'TestsController@addTestTemplate'); // szablony testów
    Route::get('/showTestTemplate', 'TestsController@showTestTemplate'); //wyświetlenie szablonów
    Route::get('/deleteshowTestTemplateTemplate/{id}', 'TestsController@deleteTemplate'); //usunięcie szablonów
    Route::get('/viewTestTemplate/{id}', 'TestsController@viewTestTemplate'); //edycja szablonów


    Route::get('/all_tests', 'TestsController@allTestsGet');
    //TESTS STOP//


    /**REKRUTACJA START */
    Route::get('add_group_training', 'GroupTrainingController@add_group_training');

    Route::get('add_group_training_2', 'GroupTrainingController@add_group_training_2');


    Route::get('/add_candidate', 'CandidateController@add_candidate');
    Route::get('/candidateProfile/{id}', 'CandidateController@candidateProfile');

    Route::get('/recruitment_resources', 'RecruitmentAttemptController@recruitment_resources');

    Route::get('/all_candidates', 'CandidateController@all_candidates');

    Route::get('/interviews_all', 'RecruitmentAttemptController@interviewsAllGet');

    Route::get('/recruitment_admin', 'RecruitmentAttemptController@recruitment_admin');


    Route::get('/recruitment_statistics_leader', 'RecruitmentAttemptController@recruitment_statistics_leaderGET');

    Route::get('/pageReportInterviews', 'RecruitmentStoryController@pageReportInterviewsGet');
    Route::post('/pageReportInterviews', 'RecruitmentStoryController@pageReportInterviewsPost');

    Route::get('/pageReportNewAccount', 'RecruitmentStoryController@pageReportNewAccountGet');
    Route::post('/pageReportNewAccount', 'RecruitmentStoryController@pageReportNewAccountPost');

    Route::get('/pageReportTraining', 'RecruitmentStoryController@pageReportTrainingGet');

    Route::get('/pageReportRecruitmentFlow', 'RecruitmentStoryController@pageReportRecruitmentFlowGet');
    Route::post('/pageReportRecruitmentFlow', 'RecruitmentStoryController@pageReportRecruitmentFlowPost');


    /**REKRUTACJA STOP */


    /** Tabela Postępów Coaching */

    Route::get('/progress_table', 'CoachingController@progress_tableGET');
    Route::get('/progress_table_for_coach', 'CoachingController@progress_table_for_coachGET');
    Route::get('/progress_table_for_director', 'CoachingController@progress_table_for_directorGET');
    Route::get('/progress_table_for_manager', 'CoachingController@progress_table_for_managerGET');
    Route::get('/progress_admin', 'CoachingController@progress_adminGET');
    // dla trenerów Stary
    Route::get('/pageReportCoaching', 'StatisticsController@pageReportCoachingGet');
    Route::post('/pageReportCoaching', 'StatisticsController@pageReportCoachingPost');

    // dla trenerów Nowy

    Route::get('/pageReportCoachingCoach', 'StatisticsController@pageReportCoachingCoachGet');
    Route::post('/pageReportCoachingCoach', 'StatisticsController@pageReportCoachingCoachPost');

    // dla kierowników
    Route::get('/pageReportCoachingManager', 'StatisticsController@pageReportCoachingManagerGet');
    Route::post('/pageReportCoachingManager', 'StatisticsController@pageReportCoachingManagerPost');
    // dla dyrektorów
    Route::get('/pageReportCoachingDirector', 'StatisticsController@pageReportCoachingDirectorGet');
    Route::post('/pageReportCoachingDirector', 'StatisticsController@pageReportCoachingDirectorPost');

    Route::get('/progress_table_manager', 'CoachingController@progress_table_managerGET');

    Route::get('/coachAscription', 'CoachingController@coachAscriptionGet');

    /** AUDYTY **/
    Route::get('/addAudit', 'AuditController@auditMethodGet');
    Route::post('/addAudit', 'AuditController@auditMethodPost');
    Route::post('/handleForm', 'AuditController@handleFormPost');
    Route::get('/showAudits', 'AuditController@showAuditsGet');
    Route::get('/audit/{id}', 'AuditController@editAuditGet');
    Route::post('/handleEdit', 'AuditController@editAuditPost');
    Route::get('/editAuditTemplates', 'AdminController@editAuditTemplatesGet');
    Route::post('/addTemplate', 'AdminController@addTemplatePost');
    Route::get('/editAudit/{id}', 'AdminController@editAuditGet');
    Route::post('/editAuditPage', 'AdminController@editDatabasePost');

    /** KONIEC AUDYTY **/

    //dodawanie usuwanie przywilejów dla użytkowników
    Route::get('/userPrivilages', 'AdminController@userPrivilagesGET');
    Route::post('/userPrivilages', 'AdminController@userPrivilagesPOST');

    /** CRM **/
    Route::get('/specificRoute/{id}', 'CrmRouteController@specificRouteGet');
    Route::get('/showClientRoutes', 'CrmRouteController@showClientRoutesGet');

    Route::get('/editRouteTemplates/{id}', 'CrmRouteController@editRouteTemplatesGet');
    Route::post('/editRouteTemplates/{id}', 'CrmRouteController@editRouteTemplatesPost');

    Route::get('/addNewRouteTemplate', 'CrmRouteController@addNewRouteTemplateGet');
    Route::post('/addNewRouteTemplate', 'CrmRouteController@addNewRouteTemplatePost');

    Route::get('/assigningRoutesToClients', 'CrmRouteController@assigningRoutesToClientsGet');
    Route::post('/assigningRoutesToClients', 'CrmRouteController@assigningRoutesToClientsPost');

    Route::get('/editAssignedRoute/{id}', 'CrmRouteController@editAssignedRouteGet');
    Route::post('/editAssignedRoute/{id}', 'CrmRouteController@editAssignedRoutePost');

    Route::post('/editRoute', 'CrmRouteController@editRoute');
    Route::get('/route/{id}', 'CrmRouteController@routeGet');

    Route::get('/showRoutes', 'CrmRouteController@showRoutesGet');

    Route::get('/addNewHotel', 'CrmRouteController@addNewHotelGet');
    Route::post('/addNewHotel', 'CrmRouteController@addNewHotelPost');
    Route::get('/showHotels', 'CrmRouteController@showHotelsGet')->name('showHotels');
    Route::get('/showHotels/{hotelId}', 'CrmRouteController@showHotelsGet')->name('showHotels');
    Route::get('/downloadHotelFiles/{id}', 'CrmRouteController@downloadHotelFiles');
    Route::get('/hotel/{id}', 'CrmRouteController@hotelGet');
    Route::post('/hotel/{id}', 'CrmRouteController@hotelPost');

    Route::get('/cityPanel', 'CrmRouteController@cityPanel');
    Route::get('/clientPanel', 'ClientController@clientPanel');

    Route::get('/showRoutesDetailed', 'CrmRouteController@showRoutesDetailedGet'); //
    Route::get('/aheadPlanning', 'CrmRouteController@aheadPlanningGet'); //

    Route::get('/showCitiesStatistics', 'CrmRouteController@showCitiesStatisticsGet');

    Route::get('/presentationStatistics', 'CrmRouteController@presentationStatisticsGet');
    Route::post('/presentationStatistics', 'CrmRouteController@presentationStatisticsPost');

    Route::get('/showClientRouteInfo', 'CrmRouteController@getClientRouteInfo');

    Route::get('/getCampaignsInvoices', 'CrmRouteController@getCampaignsInvoices');
    Route::get('/getCampaignsInvoices/{id}', 'CrmRouteController@getCampaignsInvoices');
    Route::GET('/downloadCampaignInvoicePDF/{id}', 'CrmRouteController@downloadCampaignInvoicePDF');

    Route::get('/hotelConfirmationGet', 'CrmRouteController@hotelConfirmationGet');
    /** KONIEC CRM **/

});
/**OUT OF FIREWALL **/

//CHARTS
Route::get('/charts', 'ScreensController@showScreensGet');
//ENDCHARTS

//SCREENS
Route::get('/dept/{id}', 'ScreensController@monitorMethod');
Route::get('/screen_table', 'ScreensController@screenMethod');
//END SCREENS

//Pobieranie danych dla PBX_REPORT_EXTENSION
Route::get('/pbx_report_ext', 'PBXDataAPI@PBXReportExtension');
Route::get('/temp_pbx_report_ext', 'PBXDataAPI@tempPBXReportExtension');

//Dane DKJ dla oddziałów
Route::get('/TeamDKJHourData', 'PBXDataAPI@TeamDKJHourData');
//Dane Czasu na rekord
Route::get('/TimeOnRecordData', 'PBXDataAPI@TimeOnRecordData');

//campaign reports mothod for database
Route::get('/report_campaign', 'PBXDataAPI@report_campaign');

Route::get('/pbxDetailedReport', 'PBXDataAPI@pbx_detailed_campaign_report');

//Dane z Aktuanlnego stanu kampanii
Route::get('/pbx_crm_info', 'PBXDataAPI@pbx_crm_info');
/**END OUT OF FIREWALL**/


//////////////////////Testing ORM///////////////
///
///
//COACHINGS
//Route::get('/pageReportCoachingWeekSummary', 'StatisticsController@pageReportCoachingSummaryGet'); //do usunięcia, stary
//Route::post('/pageReportCoachingWeekSummary', 'StatisticsController@pageReportCoachingSummaryPost');//do usunięcia, stary
//Route::get('/ReportCoachingSummary', 'StatisticsController@MailReportCoachingSummary'); //do usunięcia, stary

Route::get('/ReportCoaching', 'StatisticsController@MailpageReportCoaching');
Route::get('/MailToDirectors', 'StatisticsController@MailToEveryDirector');
//END COACHINGS

//pobieranie danych po oddziałach godzinny
Route::get('/testorm', 'TestORM@test');

Route::get('/progress_table_manager_for_all', 'CoachingController@progress_table_managerAllGET');

Route::get('/addNewCampaigns', 'CampaignsController@addNewCampaignsGet');
Route::post('/addNewCampaigns', 'CampaignsController@addNewCampaignsPost');

//Skrypty
Route::get('/autoChangeRouteStatus', 'AutoScriptController@autoChangeRouteStatus');
Route::get('/autoConsultantsLoginsChecking', 'StatisticsController@MailAutoConsultantsLoginsChecking');

Route::get('/autoConsultantsLoginsCheckingGet', 'StatisticsController@autoConsultantsLoginsCheckingGet');
Route::get('/autoConsultantsLoginsBlockedGet', 'StatisticsController@autoConsultantsLoginsBlockedGet');
Route::get('/checkPenatly', 'AutoScriptController@checkPenatly');

Route::POST('/clientReport', 'CrmRouteController@clientReport')->name('api.clientReport');

Route::post('/changeLimits', 'CrmRouteController@changeLimitsAjax')->name('api.changeLimits');

