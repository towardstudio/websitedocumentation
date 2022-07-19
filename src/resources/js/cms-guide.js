import { createApp } from "vue";
import Guide from "./_components/Guide.vue";

const sections = window.cmsGuideSections;
const accentBgColor = window.accentBgColor;
const accentTextColor = window.accentTextColor;

// Sidebar
createApp(Guide, {
	sections: sections,
	bgColor: accentBgColor,
	textColor: accentTextColor
}).mount('#sidebar-content');

// Components
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
