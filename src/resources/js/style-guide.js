import { createApp } from "vue";
import Sidebar from "./_components/Sidebar.vue";

const sections = window.styleGuideSections;
const accentBgColor = window.accentBgColor;
const accentTextColor = window.accentTextColor;

// Sidebar
createApp(Sidebar, {
	sections: sections,
	bgColor: accentBgColor,
	textColor: accentTextColor
}).mount('#sidebar-content');

// Components
const websiteUrl = new URL(location.href);
const canvases = document.querySelectorAll("#canvas");
const sidebar = document.getElementById("aside");
const menuTrigger = document.querySelector("[data-burger]");
const menuWrapper = document.getElementById("sidebar-content");
let menuOpen = false;

// Breakpoints
const breakpoints = document.querySelectorAll('[data-breakpoint]');
const fullScreen = document.querySelector('[data-fullscreen]');
const defaultSize = document.querySelector('[data-default]');
const copyLink = document.querySelector('[data-copy-link]');

// Fullscreen
let fullSize = false;

menuTrigger.addEventListener("click", function (e) {
	if (!menuOpen) {
		menuTrigger.classList.add('open');
		menuWrapper.classList.add('open');
		menuOpen = true;
	} else {
		menuTrigger.classList.remove('open');
		menuWrapper.classList.remove('open');
		menuOpen = false;
	}
});

// Check for Query Strings
const queryStrings = window.location.search;

if (queryStrings) {
	const params = new URLSearchParams(queryStrings);
	for(var value of params.keys()) {

		if (value === 'fullscreen') {
			sidebar.style.display = 'none';
			hideSidebar();
			const fullscreenControl = document.querySelector('[data-controls] [data-fullscreen]');
			fullscreenControl.classList.add('active');
			fullSize = true;

			// Hacky, but need to remove the display none
			setTimeout(function() {
  				sidebar.style.display = null;
			}, 200);

		}

		if (value === 'breakpoint') {
			const width = params.get(value);
			const widthControl = document.querySelector(`[data-controls] [data-breakpoint="${width}"]`);

			[...widthControl.parentElement.children].forEach(sib => sib.classList.remove('active'));
			widthControl.classList.add('active');

			canvases.forEach(function (canvas) {
				canvas.style.width = width + 'px';
			});
		}
	}
}

// Check for Hashtag change
document.addEventListener("section-change", function(event) {
	websiteUrl.hash = event.detail;
	console.log(websiteUrl);
});;

// Breakpoint Function
breakpoints.forEach(function (breakpoint) {
	breakpoint.addEventListener("click", function (e) {
		const width = breakpoint.dataset.breakpoint;
		let path = '';
		const hash = window.location.hash.split('?');

		// Add Breakpoint to URL Query
		if (!event.target.classList.contains('active')) {
			websiteUrl.searchParams.set('breakpoint', width);
		} else {
			websiteUrl.searchParams.delete('breakpoint');
		}

		history.pushState(null, '', websiteUrl);

		// Change the Canvas Side
		canvases.forEach(function (canvas) {

			if (canvas.style.width === width + 'px') {
				canvas.style.width = '100%';
				breakpoint.classList.remove('active');
			} else {
				canvas.style.width = width + 'px';
				[...breakpoint.parentElement.children].forEach(sib => sib.classList.remove('active'));
				breakpoint.classList.add('active');
			}
		});
	});
});

fullScreen.addEventListener("click", function (e) {
	// Sidebar Display
	if (fullSize === true) {
		showSidebar();
		fullScreen.classList.remove('active');
		fullSize = false
	} else {
		hideSidebar();
		fullScreen.classList.add('active');
		fullSize = true
	}
});

defaultSize.addEventListener("click", function (e) {
	showSidebar();
	fullScreen.classList.remove('active');
	fullSize = false
});

copyLink.addEventListener("click", function (e) {
	copyToClipboard()
});

function hideSidebar() {
	sidebar.style.marginLeft = "-300px";
	sidebar.style.left = "-300px";
	defaultSize.style.display = 'flex';

	websiteUrl.searchParams.set('fullscreen', 'true');
	history.pushState(null, '', websiteUrl);
}

function showSidebar() {
	sidebar.style.marginLeft = "0";
	sidebar.style.left = "0";
	defaultSize.style.display = 'none';

	websiteUrl.searchParams.delete('fullscreen');
	history.pushState(null, '', websiteUrl);
}

function removeActive() {
	canvases.forEach(function (canvas) {
		canvas.style.width = '100%';
	});
	breakpoints.forEach(function (breakpoint) {
		breakpoint.classList.remove('active');
	});
}

function copyToClipboard() {
	const input = document.body.appendChild(document.createElement("input"));
	input.value = window.location.href;
	input.focus();
	input.select();
	document.execCommand('copy');
	input.parentNode.removeChild(input);
}
