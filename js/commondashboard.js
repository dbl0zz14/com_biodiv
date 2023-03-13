

//import { PDFDocument } from 'pdf-lib'


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


	
function spinImage () {
	
	let countJumps = 0;
	let animation = anime({
		autoplay: false,
		targets: [".spinImage"],
	 
		keyframes: [
			{ scaleX: 0.9, translateX:0 },
			{ scaleX: 0.5, translateX:0 },
			{ scaleX: 0.0, translateX:0 },
			{ scaleX: 0.5, translateX:0 },
			{ scaleX: 1.0, translateY:0 },
			{ scaleX: 0.5, translateX:0 },
			{ scaleX: 0.0, translateX:0 },
			{ scaleX: 0.5, translateX:0 },
			{ scaleX: 1.0, translateY:0 },
			{ scaleX: 0.5, translateX:0 },
			{ scaleX: 0.0, translateX:0 },
			{ scaleX: 0.5, translateX:0 },
			{ scaleX: 1.0, translateY:0 }
		  ],
	 
		duration: 1000,
		easing: 'easeOutQuad'
	});

	animation.restart();
	
};


	
function expandImage () {
	
	let countJumps = 0;
	let animate = anime({
		autoplay: false,
		targets: [".expandImage"],
	 
		keyframes: [
			{ scaleY: 1.0, scaleX: 1.0, translateY:0 },
			{ scaleY: 1.2, scaleX: 1.2, translateY:10 },
			{ scaleY: 1.0, scaleX: 1.0, translateY:0 }
		  ],
	 
		duration: 1000,
		easing: 'easeOutQuad',
		complete: () => {
			countJumps += 1;
			if ( countJumps < 2 ) {
				animate.restart();
			}
		}
	});

	animate.restart();
	
};



function wobbleImage () {
	
	let countJumps = 0;
	let bounceUp = anime({
		autoplay: false,
		targets: [".wobbleImage"],
	 
		keyframes: [
			{ rotate: -10 },
			{ rotate: 9, translateY:-2 },
			{ rotate: -8, translateY:-4 },
			{ rotate: 7, translateY:-6 },
			{ rotate: -6, translateY:-8 },
			{ rotate: 5, translateY:-6 },
			{ rotate: -4, translateY:-5 },
			{ rotate: 3, translateY:-4 },
			{ rotate: -2, translateY:-2 },
			{ rotate: 1, translateY:-1 },
			{ rotate: 0, translateY:0 }
		  ],
	 
		duration: 1000,
		easing: 'easeOutQuad'
		// complete: () => {
			// countJumps += 1;
			// if ( countJumps < 4 ) {
				// bounceUp.restart();
			// }
		// }
	});

	bounceUp.restart();
	
};




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


// function badgesLoaded () {
	
	// setReloadPage();
	
	// jQuery(".badge_card").click( function () {
		
		// let badgeCardId = this.id;
		// let idbits = badgeCardId.split("_");
		// let badgeId = idbits.pop();
		
		// let tasksShown = jQuery('#badge_tasks_' + badgeId).text();
		
		// if ( tasksShown ) {
			// emptyTasks ( badgeId );
		// }
		// else {
			// displayTasks ( badgeId );
		// }
	
	// });
// }



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
	




function displayHelpArticle () {
	
	let taskCardId = this.id;
	let idbits = taskCardId.split("_");
	//let articleId = idbits.pop();
	let helpType = idbits.pop();
	
	jQuery('#helpArticle').empty();
	
	//let url = BioDiv.root + "&view=article&format=raw&id=" + articleId;
	let url = BioDiv.root + "&view=helparticle&format=raw&type=" + helpType;
	jQuery('#helpArticle').load(url, setReloadPage);
	
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



// function setUploadButtons () {
	
	// setUploadButton();
	// setTaskUploadButton();
	
	// jQuery(".doneNoFiles").click( function() {
		// let noFilesId = this.id;
		// let idbits = noFilesId.split("_");
		// let taskId = idbits.pop();
		
		// let url = BioDiv.root + "&view=updatetask&format=raw&done=1&id=" + taskId;
		// jQuery('#displayArea').load(url, taskDoneOrUploaded);
		// //jQuery('#uploadTask').load(url, taskDoneOrUploaded);
	// });
	
	
// }

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
	
	jQuery(this).load(schoolTargetUrl, setReloadPage);
}

// -------------------------------------- end of school target stuff



// -------------------------------------- Student target

function loadStudentTarget () {
	
	let studentTargetUrl = BioDiv.root + "&view=studenttarget&format=raw";
	
	jQuery(this).load(studentTargetUrl, setReloadPage);
}

// -------------------------------------- end of student target


// -------------------------------------- Student celebration

function loadStudentCelebration () {
	
	let url = BioDiv.root + "&view=celebration&format=raw";
	
	jQuery(this).load(url, setReloadPage);
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
	
	
function loadResourceSet () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	
	const href = "bes-resource-set?set_id=" + setId;
	
	window.location.href = href;
}


function toggleLike () {
	
	//e.preventDefault();
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	if ( jQuery("#resourceSet_" + setId).hasClass("likedByMe") ) {
		unlikeSetId ( setId);
	}
	else {
		likeSetId ( setId );
	}
	
}

function likeSet ( e ) {
	
	e.preventDefault();
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	likeSetId ( setId );
}


function unlikeSet ( e ) {
	
	e.preventDefault();
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	unlikeSetId ( setId );
	
}

function likeSetId ( setId ) {
	
	jQuery("#likeSet_" + setId).hide();
	jQuery("#unlikeSet_" + setId).show();
	jQuery("#resourceSet_" + setId).addClass ( "likedByMe" );
	let likeUrl = BioDiv.root + "&view=likeresourceset&format=raw&like=1&id=" + setId;
	jQuery("#numSetLikes_" + setId).load(likeUrl);
	
}


function unlikeSetId ( setId ) {
	
	jQuery("#unlikeSet_" + setId).hide();
	jQuery("#likeSet_" + setId).show();
	jQuery("#resourceSet_" + setId).removeClass ( "likedByMe" );
	let likeUrl = BioDiv.root + "&view=likeresourceset&format=raw&like=0&id=" + setId;
	jQuery("#numSetLikes_" + setId).load(likeUrl);
}


function faveSet () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	faveSetId ( setId );
}


function unfaveSet () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	unfaveSetId ( setId );
	
}

function faveSetId ( setId ) {
	
	jQuery("#faveSet_" + setId).hide();
	jQuery("#unfaveSet_" + setId).show();
	jQuery("#resourceSet_" + setId).addClass ( "favedByMe" );
	let faveUrl = BioDiv.root + "&view=favouriteresourceset&format=raw&fave=1&id=" + setId;
	jQuery.ajax(faveUrl);
	
}


function unfaveSetId ( setId ) {
	
	jQuery("#unfaveSet_" + setId).hide();
	jQuery("#faveSet_" + setId).show();
	jQuery("#resourceSet_" + setId).removeClass ( "favedByMe" );
	let faveUrl = BioDiv.root + "&view=favouriteresourceset&format=raw&fave=0&id=" + setId;
	jQuery.ajax(faveUrl);
}


function pinSet () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	pinSetId ( setId );
}


function unpinSet () {
	
	let id = jQuery(this).attr("id");
	let idbits = id.split("_");
	let setId = idbits.pop();
	unpinSetId ( setId );
	
}

function pinSetId ( setId ) {
	
	jQuery("#pinSet_" + setId).hide();
	jQuery("#unpinSet_" + setId).show();
	let faveUrl = BioDiv.root + "&view=pinresourceset&format=raw&pin=1&id=" + setId;
	jQuery.ajax(faveUrl);
	
}


function unpinSetId ( setId ) {
	
	jQuery("#unpinSet_" + setId).hide();
	jQuery("#pinSet_" + setId).show();
	let faveUrl = BioDiv.root + "&view=pinresourceset&format=raw&pin=0&id=" + setId;
	jQuery.ajax(faveUrl);
}


function displayReadOnlyBadgeArticle () {
	
	jQuery("#badgeModal").modal('show');
	
	let badgeBtnId = this.id;
	let idbits = badgeBtnId.split("_");
	let badgeId = idbits.pop();
	
	jQuery('#badgeArticle').empty();
	
	let url = BioDiv.root + "&view=badgearticle&format=raw&readonly=1&id=" + badgeId;
	jQuery('#badgeArticle').load(url, badgeArticleLoaded);
	
};


function displayBadgeCompleteArticle () {
	
	jQuery("#badgeModal").modal('show');
	
	let badgeBtnId = this.id;
	let idbits = badgeBtnId.split("_");
	let badgeId = idbits.pop();
	
	jQuery('#badgeArticle').empty();
	
	let url = BioDiv.root + "&view=badgearticle&format=raw&complete=1&id=" + badgeId;
	jQuery('#badgeArticle').load(url, badgeArticleLoaded);
	
};


function badgeArticleLoaded () {
	
	setReloadPage();
		
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
	
	let scaleRequired = 1.2 * imgWidth / pdfOriginalWidth;
	
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

async function modifyPdf() {
      // Fetch an existing PDF document
      const url = 'https://pdf-lib.js.org/assets/with_update_sections.pdf'
  		const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())

      // Load a PDFDocument from the existing PDF bytes
      const pdfDoc = await PDFDocument.load(existingPdfBytes)

      // Embed the Helvetica font
      const helveticaFont = await pdfDoc.embedFont(StandardFonts.Helvetica)

      // Get the first page of the document
      const pages = pdfDoc.getPages()
      const firstPage = pages[0]

      // Get the width and height of the first page
      const { width, height } = firstPage.getSize()

      // Draw a string of text diagonally across the first page
      firstPage.drawText('This text was added with JavaScript!', {
        x: 5,
        y: height / 2 + 300,
        size: 50,
        font: helveticaFont,
        color: rgb(0.95, 0.1, 0.1),
        rotate: degrees(0),
      })

      // Serialize the PDFDocument to bytes (a Uint8Array)
      const pdfBytes = await pdfDoc.save()
	  
}


async function loadCertificate() {
	
	//await import("https://unpkg.com/pdf-lib@1.4.0/dist/pdf-lib.min.js");
	//await import(PDFDocument);
	
	const pdfUrl = jQuery(this).data("pdfurl");
	const pdfName = jQuery(this).data("pdfname");
	const pdfDate = jQuery(this).data("pdfdate");
	
	const existingPdfBytes = await fetch(pdfUrl).then(res => res.arrayBuffer());
	
	// Load a PDFDocument from the existing PDF bytes
	//const pdfDoc = await PDFLib.PDFDocument.create();
    const pdfDoc = await PDFLib.PDFDocument.load(existingPdfBytes);
	
	// Embed the Helvetica font
    const helveticaFont = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);
	
	// Get the first page of the document
	const pages = pdfDoc.getPages();
	const firstPage = pages[0];

	// Get the width and height of the first page
	const { width, height } = firstPage.getSize();

	// What's our text width
	const textWidth = helveticaFont.widthOfTextAtSize(pdfName, 30);
	const textStart = (width - textWidth) / 2;
	
	firstPage.drawText(pdfName, {
		x: textStart,
		y: height * 0.75 ,
		size: 30,
		font: helveticaFont,
		color: PDFLib.rgb(0.3,0.3,0.3),
		rotate: PDFLib.degrees(0),
	})
	
	firstPage.drawText(pdfDate, {
		x: width*0.75,
		y: height * 0.25 ,
		size: 14,
		font: helveticaFont,
		color: PDFLib.rgb(0.3,0.3,0.3),
		//rotate: PDFLib.degrees(0),
	})
	
	const viewerPrefs = pdfDoc.catalog.getOrCreateViewerPreferences();
	viewerPrefs.setHideToolbar(true);
	viewerPrefs.setHideMenubar(true);
	viewerPrefs.setHideWindowUI(true);
	
	const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
	document.getElementById('besCertificate').src = pdfDataUri;
	  
	  
	  
	// const url = 'https://pdf-lib.js.org/assets/with_update_sections.pdf'
  		// const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())

      // // Load a PDFDocument from the existing PDF bytes
      // const pdfDoc = await PDFDocument.load(existingPdfBytes)

      // // Embed the Helvetica font
      // const helveticaFont = await pdfDoc.embedFont(StandardFonts.Helvetica)

      // // Get the first page of the document
      // const pages = pdfDoc.getPages()
      // const firstPage = pages[0]

      // // Get the width and height of the first page
      // const { width, height } = firstPage.getSize()

      // // Draw a string of text diagonally across the first page
      // firstPage.drawText('This text was added with JavaScript!', {
        // x: 5,
        // y: height / 2 + 300,
        // size: 50,
        // font: helveticaFont,
        // color: rgb(0.95, 0.1, 0.1),
        // rotate: degrees(0),
      // })

      // // Serialize the PDFDocument to bytes (a Uint8Array)
      // const pdfBytes = await pdfDoc.save()

			// // Trigger the browser to download the PDF document
      // download(pdfBytes, "pdf-lib_modification_example.pdf", "application/pdf");
    // }
	
	
	// const pdfDoc = await PDFLib.PDFDocument.create();
	// const page = pdfDoc.addPage([350, 400]);
	// page.moveTo(110, 200);
	// page.drawText('Hello World!');
	// const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
	// document.getElementById('pdf').src = pdfDataUri;
}


function toggleActiveFilter () {
	
	let allTogglable = jQuery(".toggleActive");
	allTogglable.removeClass("activeFilterPanel");
	allTogglable.find("img").each( function () {
		let icon = jQuery(this).data("icon");
		jQuery(this).attr("src", icon); 
	});
	
	let currElement = jQuery(this);
	let currImage = currElement.find("img");
	let activeIcon = currImage.data("activeicon");
	
	currElement.addClass("activeFilterPanel");
	currImage.attr("src", activeIcon );
}	



	
jQuery(document).ready(function(){
	
	setReloadPage();
	
	jQuery(".toggleActive").click(toggleActiveFilter);
	
	// ------------------------------- new userAgent
	
	jQuery(".avatarBtn").click( function() {
		jQuery(".avatarBtn").removeClass("active");
		jQuery(this).addClass("active");
	});
	
	jQuery(".saveAvatar").click( function() {
		
		let id = jQuery(this).attr("id");
		let idbits = id.split("_");
		let avatarId = idbits.pop();
		
		let url = BioDiv.root + "&view=saveavatar&format=raw&id=" + avatarId;
		jQuery("#avatarSavedArea").load(url, function () {
			jQuery("#avatarSavedArea").removeClass("hidden");
			jQuery("#avatarArea").addClass("hidden");
			setReloadPage();
			jQuery("#goToDash").removeClass("hidden");
			
		});
	
		
		
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
