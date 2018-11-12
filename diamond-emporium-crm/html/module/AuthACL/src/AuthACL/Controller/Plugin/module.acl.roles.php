<?php
$resources = array(
		'Alert\Controller\Index::index',
		'Alert\Controller\Index::ajaxuseralertclear',
		'Alert\Controller\Index::ajaxuseralertcount',
		'Alert\Controller\Index::ajaxuseralertlist',
		'Alert\Controller\Index::ajaxallalertlist',
		'Customer\Controller\Index::index',
		'Customer\Controller\Index::tasks',
		'Customer\Controller\Index::dashboard',
		'Customer\Controller\Index::savemygridview',
		'Customer\Controller\Index::editgridview',
		'Customer\Controller\Index::deletegridview',
    	'Customer\Controller\Index::customerdetails',
    	'Customer\Controller\Index::ajaxpartnerslookup',
		'Customer\Controller\Index::ajaxsearchgrids',
		'Customer\Controller\Index::ajaxrecordscount',
	    'Customer\Controller\Index::savecustomer',
	    'Customer\Controller\Index::savepartner',
	    'Customer\Controller\Index::unassignpartner',
		'Customer\Controller\Index::checkviewexist',		
		'Customer\Controller\Leads::index',
    	'Customer\Controller\Leads::ajaxgetleads',		
    	'Customer\Controller\Leads::customersLookup',
	    'Customer\Controller\Leads::ajaxcheckmobile',
	    'Customer\Controller\Leads::ajaxcheckemail',
    	'Customer\Controller\Leads::leaddetails',
    	'Customer\Controller\Leads::convertleadform',
		'Customer\Controller\Leads::newleadform',
    	'Customer\Controller\Leads::convertlead',
    	'Customer\Controller\Leads::ajaxcustomerfromlead',
    	'Customer\Controller\Leads::leadopportunitylookup',
		'Customer\Controller\Leads::ajaxcustomerslookup',
		'Customer\Controller\Leads::ajaxoppcustomerslookup',
		'Customer\Controller\Leads::sendmailtoleadowner',
		'Customer\Controller\Leads::updateleadstatus',
                'Customer\Controller\Leads::updateleadstatusFromDashboard',
    	'Customer\Controller\Index::ajaxcustomerslist',
    	'Customer\Controller\Index::createcustomer',
		'Customer\Controller\Index::checkduplicate',
		'Customer\Controller\Index::upoadprofilephoto',
		'Customer\Controller\Leads::webleads',
		'Customer\Controller\Leads::webtoleadsuccess',
    'Customer\Controller\Leads::ajaxCreateLeadFromDashboard',
                'Customer\Controller\Index::ajaxCreateCustomerDashboard',
		'Inventory\Controller\Index::index',
		'Inventory\Controller\Index::savediamond',
		'Inventory\Controller\Index::saveandconsigndiamond',
		'Inventory\Controller\Index::ajaxsupplierslookup',
		'Inventory\Controller\Index::ajaxgetdiamonds',
		'Inventory\Controller\Index::uploadfile',
		'Inventory\Controller\Index::saveconsign',
		'Inventory\Controller\Index::validateowner',
		'Inventory\Controller\Index::consignform',
		'Inventory\Controller\Index::getconsigndetails',
		'Inventory\Controller\Index::diamonddetails',
		'Inventory\Controller\Index::uploadmultiplefile',
		'Inventory\Controller\Index::unlinkfile',
		'Inventory\Controller\Weddingring::index',
		'Inventory\Controller\Weddingring::saveweddingring',
		'Inventory\Controller\Weddingring::ajaxgetweddingrings',
		'Inventory\Controller\Weddingring::weddingringdetails',
		'Inventory\Controller\Weddingring::getadditionallist',
		'Inventory\Controller\Engagementring::index',
		'Inventory\Controller\Engagementring::saveengagementring',
		'Inventory\Controller\Engagementring::ajaxgetengagementrings',
		'Inventory\Controller\Engagementring::engagementringdetails',
		'Inventory\Controller\Earring::index',
		'Inventory\Controller\Earring::saveearring',
		'Inventory\Controller\Earring::ajaxgetearrings',
		'Inventory\Controller\Earring::earringdetails',
		'Inventory\Controller\Pendant::index',
		'Inventory\Controller\Pendant::savependant',
		'Inventory\Controller\Pendant::ajaxgetpendants',
		'Inventory\Controller\Pendant::pendantdetails',
		'Inventory\Controller\Miscellaneous::index',
		'Inventory\Controller\Miscellaneous::savemiscellaneous',
		'Inventory\Controller\Miscellaneous::ajaxgetmiscellaneous',
		'Inventory\Controller\Miscellaneous::miscellaneousdetails',
		'Inventory\Controller\Index::inventoryjobs',
		'Invoice\Controller\Index::index',
		'Invoice\Controller\Index::newquotes',
		'Invoice\Controller\Index::newinvoice',
		'Invoice\Controller\Index::ajaxInvoice',
		'Invoice\Controller\Index::ajaxQuote',
		'Invoice\Controller\Index::duplicateinvoice',
		'Invoice\Controller\Index::duplicatequote',
		'Invoice\Controller\Index::copytoinvoice',
		'Invoice\Controller\Index::emailinvoice',
		'Invoice\Controller\Index::composeinvoiceemail',
		'Invoice\Controller\Index::ajaxgetinvoiceemail',
		'Invoice\Controller\Index::viewinvoiceemail',
		'Invoice\Controller\Index::replyemail',
		'Invoice\Controller\Index::editinvoicequotes',
		'AuthACL\Controller\Index::logout',
		'Opportunities\Controller\Index::index',
		'Opportunities\Controller\Index::ajaxgetopportunities',
		'Opportunities\Controller\Index::opportunitydetails',
		'Opportunities\Controller\Index::updateopportunitystatus',
		'Notes\Controller\Index::ajaxgetnotes',
		'Notes\Controller\Index::notes',
		'Task\Controller\Index::gettaskdetails',
		'Task\Controller\Index::index',
		'Task\Controller\Index::createtask',
		'Task\Controller\Index::updateindividual',		
		'Task\Controller\Index::savecomment',
		'Task\Controller\Index::attachfile',
		'Task\Controller\Index::gettaskhistorydetails',		
		'Task\Controller\Index::saveattachments',
		'Task\Controller\Index::downloadattachment',
		'Task\Controller\Index::changetaskstatus',
		'Task\Controller\Index::editcomment',
		'Task\Controller\Index::getsubjects',
		'Suppliers\Controller\Index::index',
		'Suppliers\Controller\Index::ajaxgetsuppliers',
		'Suppliers\Controller\Index::supplierdetails',
		'Suppliers\Controller\Index::newsuppliersform',
		'Inventory\Controller\Websites::index',
		'Inventory\Controller\Chain::index',
		'Inventory\Controller\Chain::savechain',
		'Inventory\Controller\Chain::ajaxgetchain',
		'Inventory\Controller\Chain::chaindetails',
		'Order\Controller\Index::index',
		'Order\Controller\Index::ajaxinvoicelookup',
		'Order\Controller\Index::createorder',
		'Order\Controller\Index::ajaxorderlist',
		'Order\Controller\Index::orderdetails',
		'Order\Controller\Index::createjobpacket',
		'Order\Controller\Index::ajaxjoblist',
		'Order\Controller\Index::jobdetails',
		'Order\Controller\Index::editorderform',
		'Order\Controller\Index::startjob',
		'Order\Controller\Index::savecaddesign',
		'Order\Controller\Index::prototypestep1',
		'Order\Controller\Index::getcaddesignstage',
		'Order\Controller\Index::prototypestep2',
		'Order\Controller\Index::caststep1',
		'Order\Controller\Index::caststep2',
		'Order\Controller\Index::workshopstep1',
		'Order\Controller\Index::savesuppliertask',
		'Order\Controller\Index::workshopqualitycontrol',
		'Order\Controller\Index::workshopfinalstep',
		'Order\Controller\Index::startjobrequest',
		'Order\Controller\Index::approvejob',
		'Order\Controller\Index::addmilestone',		
		'Order\Controller\Index::changejobstatus',
		'Order\Controller\Index::composemilestoneemail',
		'Order\Controller\Index::emailmilestone',
		'Order\Controller\Index::ajaxgetmilestoneemail',
		'Order\Controller\Index::viewmilestoneemail',
		'Order\Controller\Index::updatejobform',
		'Order\Controller\Index::downloademailattachment',
		'Order\Controller\Index::printjob',
		'Order\Controller\Index::updateworkshoptask',
		'User\Controller\Index::index',
		'User\Controller\Index::ajaxuserlist',
		'User\Controller\Index::userform',
		'User\Controller\Index::saveuser',
		'User\Controller\Index::users',
		'User\Controller\Index::checkduplicateemail',
		'User\Controller\Index::setmasterpass',
		'User\Controller\Index::emails',
    'Leave\Controller\Leave::index',
    'Leave\Controller\Leave::ajaxGetUserDetailForLeave',
    'Leave\Controller\Leave::ajaxSaveLeaves',  
    'Leave\Controller\Leave::ajaxGetAllLeaves',
    'User\Controller\Index::uploadfileimage',
    'User\Controller\Index::ajaxuserstatusupdate',
    'User\Controller\Index::ajaxGetUserStatus',
    'Dashboard\Controller\Dashboard::index',
    'Dashboard\Controller\Dashboard::add',
    'Dashboard\Controller\Dashboard::ajaxAddDashboard',
    'Dashboard\Controller\Dashboard::ajaxGetUserBasedOnBudget',
    'Dashboard\Controller\Dashboard::ajaxGetLeadsByBudget',
    'Dashboard\Controller\Dashboard::ajaxGetDataforCalender',
    'Dashboard\Controller\Dashboard::ajaxGetDataForQuestionViewCalender',
    'Dashboard\Controller\Dashboard::ajaxGetDataForCustomViewCalender',
    'Dashboard\Controller\Dashboard::ajaxUpdateleadStatus',  
    'Dashboard\Controller\Dashboard::ajaxGetTeamStatus',  
    'SaveDashboard\Controller\SaveDashboard::index',
    'Dashboard\Controller\Dashboard::ajaxGetLeadDetailForLeadPage',
    'Dashboard\Controller\Dashboard::GetNextInLine',
    'Dashboard\Controller\Dashboard::ajaxGetUserLoginDetail',
    'Dashboard\Controller\Dashboard::ajaxGetCountriesList',
    'Dashboard\Controller\Dashboard::ajaxGetUserColor',
    'Dashboard\Controller\Dashboard::ajaxGetCustomerOnLookup',
    'Dashboard\Controller\Dashboard::ajaxGetCheckUserEmail',
    'Dashboard\Controller\Dashboard::ajaxGetUserLeaves',
    'Dashboard\Controller\Dashboard::ajaxCheckUserIsOnLeave',
    'Dashboard\Controller\Dashboard::ajaxGetDataListofSalesRep',
    'Dashboard\Controller\Dashboard::checkLeadEmail',
    'Dashboard\Controller\Dashboard::ajaxGetCustomerByName',
    'Dashboard\Controller\Dashboard::ajaxGetDataForSearch',
    'Dashboard\Controller\Dashboard::ajaxGetCustomerById',
    'Dashboard\Controller\Dashboard::ajaxSaveAppointment',
    'Dashboard\Controller\Dashboard::ajaxUpdateDashboard',
    'Appointment\Controller\Appointment::index',
    'Customer\Controller\Index::ajaxcheckDuplicateEmail',
    'Dashboard\Controller\Dashboard::ajaxGetStateList',    
    'Dashboard\Controller\Dashboard::ajaxGetProductsList',
    'Dashboard\Controller\Dashboard::ajaxGetHowHeardList',
   

	);

$deleteResources = array(
		'Customer\Controller\Index::deletecustomer',
		'Inventory\Controller\Index::deletediamond',

		/*
		 * TODO: deleteadditional is used by several types of inventory, not juse wedding rings.
		 * It should be moved into a common function.
		 * Also - there is no separate handling for the delete button visibility for this.
		 */
		'Inventory\Controller\Weddingring::deleteadditional',

		'Inventory\Controller\Weddingring::deleteweddingring',
		'Inventory\Controller\Engagementring::deleteengagementring',
		'Inventory\Controller\Earring::deleteearring',
		'Inventory\Controller\Pendant::deletependant',
		'Inventory\Controller\Miscellaneous::deletemiscellaneous',
		'Invoice\Controller\Index::deletequote',
		'Invoice\Controller\Index::deleteinvoice',
		'Opportunities\Controller\Index::deleteopportunity',
		'Task\Controller\Index::deletetask',
		'Task\Controller\Index::deletecomment',
		'Suppliers\Controller\Index::deletesupplier',
		'Inventory\Controller\Chain::deletechain',
		'Order\Controller\Index::deleteorder',
		'Order\Controller\Index::deletejob',
		
		/*
		 * TODO: deletemilestone has no separate handling for hiding buttons right now.
		 * It uses delete job.
		 */
		'Order\Controller\Index::deletemilestone',

		'User\Controller\Index::deleteuser',
	);


/*
 * TODO: this should probably change to work by ID, not name.
 * That way we can change the display names at will.
 */
return array(
	'anonymous' => array(
        'AuthACL\Controller\Index::index',
		'AuthACL\Controller\Index::forgotpassword',
		'AuthACL\Controller\Index::sendresetpassurl',
		'AuthACL\Controller\Index::resetpass',
		'AuthACL\Controller\Index::storeresetpass',
		'Invoice\Controller\Index::updateinvoice',
		'Customer\Controller\Leads::webleads',
		'Customer\Controller\Leads::webtoleadsuccess',
		'Task\Controller\Index::attachfile',
		'Task\Controller\Index::saveattachments'
    ),
    'superadmin' => array_merge($resources, $deleteResources),
    'admin' => $resources,
    'manager' => $resources,
    'sales' => $resources,
    'production' => $resources,
    'accounts' => $resources,
    'service' => $resources
);
