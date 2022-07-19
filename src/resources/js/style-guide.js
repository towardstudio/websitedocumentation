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
const canvases = document.querySelectorAll("#canvas");
const sidebar = document.getElementById("aside");
const menuTrigger = document.querySelector("[data-burger]");
const menuWrapper = document.getElementById("sidebar-content");
let menuOpen = false;


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

// Breakpoints
const breakpoints = document.querySelectorAll('[data-breakpoint]');
const fullScreen = document.querySelector('[data-fullscreen]');
const defaultSize = document.querySelector('[data-default]');
const copyLink = document.querySelector('[data-copy-link]');

breakpoints.forEach(function (breakpoint) {
	breakpoint.addEventListener("click", function (e) {
		canvases.forEach(function (canvas) {
			const width = breakpoint.dataset.breakpoint;
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

let fullSize = false;

fullScreen.addEventListener("click", function (e) {
	if (fullSize === true) {
		showSidebar();
		fullScreen.classList.remove('active');
		fullSize = false
	} else {
		hideSidebar();
		removeActive();
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
}

function showSidebar() {
	sidebar.style.marginLeft = "0";
	sidebar.style.left = "0";
	defaultSize.style.display = 'none';
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
