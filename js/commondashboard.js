

function flyBee () {
	
	let path = anime.path('#path');
	
	const timeline = anime.timeline({
	  easing: 'easeInOutExpo',
	  duration: 1000,
	  complete: () => {
		  console.log("anime complete");
		  /*
		anime({
		  targets: '.leaf',
		  rotate: 40,
		  duration: 3000,
		  loop: true,
		  direction: 'alternate',
		  easing: 'easeInOutQuad'
		});
		anime({
		  targets: '.petals',
		  scale: 1.05,
		  duration: 6000,
		  loop: true,
		  direction: 'alternate',
		  easing: 'easeInOutQuad'
		});*/
	  }
	});


	timeline.add({
	  targets: '#bee',
	  opacity: [0, 1],
	}, '-=750');


	let flyb = anime({
	  targets: 'svg#bee',
	  translateX: path('x'),
	  translateY: path('y'),
	  //translateX: 250,
	  //translateY: -250,
	  rotate: path('angle'),
	  loop: false,
	  duration: 15000,
	  delay: 500,
	  easing: 'linear'
	});
	/*
	let taskCard = anime({
			targets: [taskBackSelector, "#" + taskCardId],
			//scale: [{value:1}, {value:1.3}, {value:1, delay: 250} ]
			opacity: function(target, index) {
				return opacityValues[index];
			},
			rotateY: function(target, index) {
				return rotateYValues[index];
			},
			duration: 800,
			easing: 'linear',
		});
		*/
}



	
function animateAvatar () {
	
	let countJumps = 0;
	let bounceUp = anime({
		autoplay: false,
		targets: [".jumpingAvatar"],
	 
		keyframes: [
			{ scaleY: 0.9, translateY:0 },
			{ scaleY: 0.9, translateY:-20 },
			{ scaleY: 1.0, translateY:-20 },
			{ scaleY: 1.0, translateY:0 }
		  ],
	 
		duration: 675,
		easing: 'easeOutQuad',
		complete: () => {
			countJumps += 1;
			if ( countJumps < 4 ) {
				bounceUp.restart();
			}
		}
	});

	bounceUp.restart();
	
};
	



// -------------------------------  Badges

function displayBadges ( moduleId, badgeGroup, viewOnly = false, viewTeacher = false ) {
	
	let url = BioDiv.root + "&view=badges&format=raw&module=" + moduleId + "&group=" + badgeGroup;
	
	if ( viewOnly ) {
		url += "&viewonly=1";
	}
	if ( viewTeacher ) {
		url += "&teacher=1";
	}
	
	jQuery('#displayBadges').load(url, tasksLoaded);
	
};


function displayTeacherBadges ( moduleId, badgeGroup ) {
	
	let url = BioDiv.root + "&view=badges&format=raw&module=" + moduleId + "&group=" + badgeGroup + "&viewonly=1&teacher=1";
	
	jQuery('#displayBadges').load(url, tasksLoaded);
	
};


function displayCollected () {
	
	let url = BioDiv.root + "&view=badges&format=raw&display=1"
	
	jQuery('#displayArea').load(url, tasksLoaded);
	
};


function collectBadges () {
	
	let url = BioDiv.root + "&view=badges&format=raw&collect=1"
	
	jQuery('#displayArea').load(url, tasksLoaded);
	
};



function displayTasks ( badgeId ) {

	let url = BioDiv.root + "&view=badgetasks&format=raw&id=" + badgeId;
	
	jQuery('#badge_tasks_' + badgeId).load(url, tasksLoaded);
	
};


function emptyTasks ( badgeId ) {

	jQuery('#badge_tasks_' + badgeId).empty();
	
};


function displayAllBadges () {
	
	let url = BioDiv.root + "&view=allbadges&format=raw";
	
	jQuery('#displayArea').load(url, badgesLoaded);
	
};


function badgesLoaded () {
	
	console.log("badgesLoaded");
	
	jQuery(".badge_card").click( function () {
		
		let badgeCardId = this.id;
		let idbits = badgeCardId.split("_");
		let badgeId = idbits.pop();
		
		let tasksShown = jQuery('#badge_tasks_' + badgeId).text();
		
		if ( tasksShown ) {
			emptyTasks ( badgeId );
		}
		else {
			displayTasks ( badgeId );
		}
	
	});
}



function tasksLoaded () {
	
	jQuery(".collectTask").click ( collectTask );
	
	jQuery(".task_btn").click ( displayTaskArticle );
	
	jQuery(".species_btn").click ( displaySpeciesArticle );
	
	jQuery(".upload_task").click ( uploadTask );
	
	jQuery(".unlock_species").click ( unlockSpecies );
	
	jQuery(".collectedBadges").click ( displayCollected );
	
	jQuery(".collectBadges").click ( collectBadges );
	
}


function collectTask () {
	
	let taskCardId = this.id;
	
	// Just so we can see the back
	//jQuery(this).hide();
	
	let idbits = taskCardId.split("_");
	let taskId = idbits.pop();
	
	let url = BioDiv.root + "&view=updatetask&format=raw&collect=1&id=" + taskId;
	jQuery.ajax(url);
	
	let taskBackSelector = "#task_detail_" + taskId;
	
	jQuery(taskBackSelector).show();
	
	let opacityValues = [1,0];
	let rotateYValues = ["+=180","+180"];
	
	jQuery(taskBackSelector).css('transform','rotateY(180deg)');
	

	let taskCard = anime({
		targets: [taskBackSelector, "#" + taskCardId],
		//scale: [{value:1}, {value:1.3}, {value:1, delay: 250} ]
		opacity: function(target, index) {
			return opacityValues[index];
		},
		rotateY: function(target, index) {
			return rotateYValues[index];
		},
		duration: 800,
		easing: 'linear',
	});
	
}
	

function displayTaskArticle () {
	
	let taskCardId = this.id;
	let idbits = taskCardId.split("_");
	let taskId = idbits.pop();
	
	jQuery('#task_article').empty();
	
	let url = BioDiv.root + "&view=task&format=raw&id=" + taskId;
	jQuery('#task_article').load(url);
	
};


function displaySpeciesArticle () {
	
	let taskCardId = this.id;
	let idbits = taskCardId.split("_");
	let taskId = idbits.pop();
	
	jQuery('#species_article').empty();
	
	let url = BioDiv.root + "&view=species&format=raw&id=" + taskId;
	jQuery('#species_article').load(url);
	
};


function displayHelpArticle () {
	
	let taskCardId = this.id;
	let idbits = taskCardId.split("_");
	let articleId = idbits.pop();
	
	jQuery('#helpArticle').empty();
	
	let url = BioDiv.root + "&view=article&format=raw&id=" + articleId;
	jQuery('#helpArticle').load(url);
	
};


function uploadTask () {
	
	let taskCardId = this.id;
	let idbits = taskCardId.split("_");
	let taskId = idbits.pop();
	
	let url = BioDiv.root + "&view=taskupload&format=raw&task=" + taskId;
	jQuery('#displayArea').load(url, setUploadButtons);
}



function unlockSpecies () {
	
	let taskCardId = this.id;
	let idbits = taskCardId.split("_");
	let taskId = idbits.pop();
	
	let viewId = "#view_species_" + taskId;
	jQuery(this).hide();
	jQuery(viewId).show();
	
	let url = BioDiv.root + "&view=updatetask&format=raw&unlock=1&id=" + taskId;
	jQuery.ajax(url);
	
}



function setUploadButtons () {
	
	setUploadButton();
	setTaskUploadButton();
	
	jQuery(".doneNoFiles").click( function() {
		let noFilesId = this.id;
		let idbits = noFilesId.split("_");
		let taskId = idbits.pop();
		
		let url = BioDiv.root + "&view=updatetask&format=raw&done=1&id=" + taskId;
		jQuery('#displayArea').load(url, taskDoneOrUploaded);
		//jQuery('#uploadTask').load(url, taskDoneOrUploaded);
	});
	
	
}

function taskDoneOrUploaded () {
	
	jQuery ('.reloadBtn').click( function () {
		reloadCurrentPage();
	});
	
	animateAvatar();
	
}

/*
function browseBadges ( moduleId = 1 ) {
	
	let url = BioDiv.root + "&view=browsebadges&format=raw&module=" + moduleId;
	jQuery('#displayArea').load(url, activatebadgeButtons);
	
}
*/

function chooseModule () {
	
	let url = BioDiv.root + "&view=choosemodule&format=raw";
	jQuery('#displayArea').load(url, activateActivityButtons);
	
}

function chooseTeacherModule () {
	
	let url = BioDiv.root + "&view=choosemodule&format=raw&teacher=1";
	jQuery('#displayArea').load(url, activateActivityButtons);
	
}

function chooseStudentModule () {
	
	let url = BioDiv.root + "&view=choosemodule&format=raw&student=1";
	jQuery('#displayArea').load(url, activateActivityButtons);
	
}


function activatebadgeButtons () {
	
	jQuery('.browseGroupBtn').click( function () {
		let buttonId = this.id;
		let idbits = buttonId.split("_");
		let badgeGroupId = idbits.pop();
		let moduleId = idbits.pop();
		
		jQuery('.browseBadgesBtn').find('.panel').removeClass("active");
		
		jQuery(this).find('.panel').addClass("active");
		
		jQuery('#navEnd')[0].scrollIntoView({
			behavior: "smooth", // or "auto" or "instant"
			block: "start" // or "end"
		});
		
		displayBadges ( moduleId, badgeGroupId );
	} );
	
	jQuery('.viewGroupBtn').click( function () {
		let buttonId = this.id;
		let idbits = buttonId.split("_");
		let badgeGroupId = idbits.pop();
		let moduleId = idbits.pop();
		
		jQuery('.viewGroupBtn').find('.panel').removeClass("active");
		
		jQuery(this).find('.panel').addClass("active");
		
		jQuery('#navEnd')[0].scrollIntoView({
			behavior: "smooth", // or "auto" or "instant"
			block: "start" // or "end"
		});
		
		displayBadges ( moduleId, badgeGroupId, true );
	} );
	
	jQuery('.viewTeacherGroupBtn').click( function () {
		let buttonId = this.id;
		let idbits = buttonId.split("_");
		let badgeGroupId = idbits.pop();
		let moduleId = idbits.pop();
		
		jQuery('.viewTeacherGroupBtn').find('.panel').removeClass("active");
		
		jQuery(this).find('.panel').addClass("active");
		
		jQuery('#navEnd')[0].scrollIntoView({
			behavior: "smooth", // or "auto" or "instant"
			block: "start" // or "end"
		});
		
		displayTeacherBadges ( moduleId, badgeGroupId );
	} );
	
	jQuery('.completeTasks').click( function () {
		
		let buttonId = this.id;
		let idbits = buttonId.split("_");
		let moduleId = idbits.pop();
		
		jQuery('.browseBadgesBtn').find('.panel').removeClass("active");
		
		jQuery(this).find('.panel').addClass("active");
		
		jQuery('#navEnd')[0].scrollIntoView({
			behavior: "smooth", // or "auto" or "instant"
			block: "start" // or "end"
		});
		
		let url = BioDiv.root + "&view=badges&format=raw&module=" + moduleId + "&complete=1";
		jQuery('#displayBadges').load(url, tasksLoaded);
	});
	
	
	jQuery('.unlockedTasks').click( function () {
		
		let buttonId = this.id;
		let idbits = buttonId.split("_");
		let moduleId = idbits.pop();
		
		jQuery('.browseBadgesBtn').find('.panel').removeClass("active");
		
		jQuery(this).find('.panel').addClass("active");
		
		jQuery('#navEnd')[0].scrollIntoView({
			behavior: "smooth", // or "auto" or "instant"
			block: "start" // or "end"
		});
		
		let url = BioDiv.root + "&view=badges&format=raw&module=" + moduleId + "&unlocked=1";
		jQuery('#displayBadges').load(url, tasksLoaded);
	});
	
	
	jQuery('.suggestTask').click( function () {
		
		let buttonId = this.id;
		let idbits = buttonId.split("_");
		let moduleId = idbits.pop();
		
		jQuery('.browseBadgesBtn').find('.panel').removeClass("active");
		
		jQuery(this).find('.panel').addClass("active");
		
		jQuery('#navEnd')[0].scrollIntoView({
			behavior: "smooth", // or "auto" or "instant"
			block: "start" // or "end"
		});
		
		let url = BioDiv.root + "&view=badges&format=raw&module=" + moduleId + "&suggest=1";
		jQuery('#displayBadges').load(url, tasksLoaded);
	});
	/*
	jQuery('.browseBadges').click( function () {
		
		let id = jQuery(this).attr("id");
		let idbits = id.split("_");
		let moduleId = idbits.pop();
	
		browseBadges(moduleId);
		
	});
	*/
	
	jQuery('.helpButton').click( displayHelpArticle );
}


function activateActivityButtons () {
	
	setReloadPage();
	/*
	jQuery('.browseBadges').click( function () {
		
		let id = jQuery(this).attr("id");
		let idbits = id.split("_");
		let moduleId = idbits.pop();
	
		browseBadges(moduleId);
		
	});
	
	jQuery('.allStudentBadges').click( function () {
		
		let divId = this.id;
		let idbits = divId.split("_");
		let moduleId = idbits.pop();
	
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
		
		let url = BioDiv.root + "&view=viewbadges&format=raw&module=" + moduleId;
		jQuery('#displayArea').load(url, activatebadgeButtons);
	});
	
	jQuery('.allTeacherTasks').click( function () {
		
		let divId = this.id;
		let idbits = divId.split("_");
		let moduleId = idbits.pop();
	
		jQuery(".manageTasksBtn").removeClass("active");
		jQuery(this).addClass("active");
		
		let url = BioDiv.root + "&view=viewbadges&format=raw&teacher=1&module=" + moduleId;
		jQuery('#displayArea').load(url, activatebadgeButtons);
	});
	*/
}

// -------------------------------  End of Badges stuff




// -------------------------------------- School target stuff

function loadSchoolTarget () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let schoolId = idbits.pop();
		
	let schoolTargetUrl = BioDiv.root + "&view=schooltarget&format=raw&id=" + schoolId;
	
	jQuery(this).load(schoolTargetUrl);
}

// -------------------------------------- end of school target stuff



// -------------------------------------- Student target

function loadStudentTarget () {
	
	let studentTargetUrl = BioDiv.root + "&view=studenttarget&format=raw";
	
	jQuery(this).load(studentTargetUrl);
}

// -------------------------------------- end of student target


// -------------------------------------- Student celebration

function loadStudentCelebration () {
	
	let url = BioDiv.root + "&view=celebration&format=raw";
	
	jQuery(this).load(url);
}

// -------------------------------------- end of student celebration


	
	/*
	function loadAllProgress () {
		
		jQuery(".badge_progress").each(loadProgress);
		
	}		
	
	
	function loadProgress () {
		
		let progressId = this.id;
		
		let idbits = progressId.split("_");
		let groupId = idbits.pop();
		
		let url = BioDiv.root + "&view=badgeprogress&format=raw&group=" + groupId;
		jQuery(this).load(url);
		
	}
	*/	

function triggerForm () {
	
	jQuery(this).find("form").trigger("submit");
}
	

function reloadCurrentPage () {
	window.location.reload(true);
}
	

function setReloadPage () {	
	jQuery ('.reloadPage').click( function () {
		reloadCurrentPage();
	});
}


async function loadPdfThumb () {
	
	let pdfUrl = jQuery(this).data("pdfurl");
	
	const canvasId = jQuery(this).find('canvas').attr("id");
	let pdfThumb = jQuery(this);
	
	let imgWidth = pdfThumb.width();
	
	const loadingTask = pdfjsLib.getDocument(pdfUrl);
	
	const pdf = await loadingTask.promise;
	
	const page = await pdf.getPage(1);
	
	const scale = 1;
	const viewport = page.getViewport({ scale });
	
	
	let pdfOriginalWidth = viewport.width;
	
	let scaleRequired = 0.9 * imgWidth / pdfOriginalWidth;
	
	const thumbCanvas = document.getElementById(canvasId);
	const context = thumbCanvas.getContext("2d");

	const transform = scaleRequired !== 1 
	  ? [scaleRequired, 0, 0, scaleRequired, 0, 0] 
	  : null;

	//
	// Render PDF page into canvas context
	//
	const renderContext = {
	  canvasContext: context,
	  transform,
	  viewport,
	};
	page.render(renderContext);
		
	
	
	/*
	pdfjsLib.GlobalWorkerOptions.workerSrc =
    "../media/com_biodiv/js/pdfjs/pdf.worker.js";
	
	let pdfUrl = jQuery(this).data("pdfurl");
		
	console.log ("pdf url = " + pdfUrl);
	
	pdfjsLib.GlobalWorkerOptions.workerSrc =
    '//mozilla.github.io/pdf.js/build/pdf.worker.js';
	
	*/
}

function loadPdfThumbnails () {
	jQuery(".pdfThumb").each(loadPdfThumb);
}
	
	
jQuery(document).ready(function(){
	
	setReloadPage();
	
	
	// ------------------------------- new userAgent
	
	jQuery(".avatarBtn").click( function() {
		jQuery(".avatarBtn").removeClass("active");
		jQuery(this).addClass("active");
	});
	
	jQuery("#saveAvatar").click( function() {
		
		let activeAvatars = jQuery(".avatarBtn.active");
		if ( activeAvatars.length > 0 ) {
			let id = activeAvatars[0].id;
			let idbits = id.split("_");
			let avatarId = idbits.pop();
			
			let url = BioDiv.root + "&view=saveavatar&format=raw&id=" + avatarId;
			jQuery("#avatarArea").load(url, function () {
				jQuery("#goToDash").show();
			});
		}
		
		
	});
	
	// -------------------------------  Notifications

	jQuery(".closeNoteBtn").click( function () {
		
		let note = jQuery(this).parent();
		note.hide();
		
	});
	
	
	
	// -------------------------------  Dashboard controls
	
	jQuery('.displayBadges').click( function () {
		
		let id = jQuery(this).attr("id");
		let idbits = id.split("_");
		let badgeGroupId = idbits.pop();
		
		displayBadges ( badgeGroupId );
		
	});
	/*
	jQuery('.browseBadges').click( function () {
		
		browseBadges();
		
	});
	*/
	jQuery('.chooseModule').click( function () {
		
		chooseModule();
		
	});
	
	jQuery('.chooseTeacherModule').click( function () {
		
		chooseTeacherModule();
		
	});
	
	jQuery('.chooseStudentModule').click( function () {
		
		chooseStudentModule();
		
	});
	
	jQuery('.allBadges').click( displayAllBadges);
	
	jQuery('.messages').click( function () {
		
		let url = BioDiv.root + "&view=messages&format=raw";
		jQuery("#displayArea").load(url, activateMessageButtons);
		
	});
	
	jQuery('.helpButton').click( displayHelpArticle );
	jQuery('.menuHelpButton').click( displayHelpArticle );
	
	
	jQuery(".formPanel").click( triggerForm );
	
	jQuery(".backBtn").click( function () { history.go(-1)} );
	
	
	// ------------------------  End of dashboard controls
	
	
	// ------------------------ Initialise
	
	let eventsUrl = BioDiv.root + "&view=events&format=raw&num=30";
	jQuery("#eventLog").load(eventsUrl);
	
	jQuery(".schoolData").each(loadSchoolTarget);
	
	jQuery(".studentTarget").each(loadStudentTarget);
	
	jQuery(".studentCelebration").each(loadStudentCelebration);
	
	loadPdfThumbnails();
	
	//loadAllProgress();
	
	//animateAvatar();
	
	//flyBee();
	
	//jQuery('.displayBadges').first().trigger("click");
	
});
