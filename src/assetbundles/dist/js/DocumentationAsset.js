/**
 * title Show/Hide Content
 * description This determines which tabs to show and which to hide based on the navigation
 * param {string} tabContainer
 */
function showHideContent(tabContainer) {
	const tabContainerName = tabContainer.dataset.tabNavigation;
	const activeTab = tabContainer.querySelector(".active-tab");
	const activeTabName = activeTab.dataset.tab;
	const activeContentContainer = document.querySelector(
		'[data-tab-content="' + tabContainerName + '"]',
	);
	const activeContentBlock = activeContentContainer.querySelectorAll(
		'[data-content="' + activeTabName + '"]',
	);

	activeContentBlock.forEach(function (activeBlock) {
		const activeBlocksChildren = activeBlock.parentElement.children;
		const contentBlocks = Array.from(activeBlocksChildren);

		contentBlocks.forEach(function (contentBlock) {
			if (
				contentBlock === activeBlock &&
				contentBlock.hasAttribute("data-content")
			) {
				contentBlock.classList.add("block");
				contentBlock.classList.remove("hidden");
			} else if (
				contentBlock !== activeBlock &&
				contentBlock.hasAttribute("data-content")
			) {
				contentBlock.classList.add("hidden");
				contentBlock.classList.remove("block");
			}
		});
	});
}

/**
 * title Find the Active Content
 */
function findActiveContent() {
	const activeTabClass = "active-tab";
	const tabContainers = document.querySelectorAll("[data-tab-navigation]");

	tabContainers.forEach(function (tabContainer) {
		const activeTabs = tabContainer.querySelectorAll("." + activeTabClass);

		if (!activeTabs.length) {
			tabContainer
				.querySelector("[data-tab]")
				.classList.add(activeTabClass, "underline");
		}

		showHideContent(tabContainer);
	});

	const firstActiveTab = document.querySelector(".active-tab");
}

function toggleActiveTab(tab) {
	const activeTabClass = "active-tab";
	let navTabsChildren = tab.parentElement.children;

	if (tab.hasAttribute("data-tab-inner")) {
		const tabNavigation = document.querySelector(
			'[data-tab-navigation="' + tab.dataset.tabInner + '"]',
		);
		navTabsChildren = tabNavigation.querySelectorAll("[data-tab]");
	}

	const navTabs = Array.from(navTabsChildren);

	navTabs.forEach(function (navTab) {
		if (navTab.dataset.tab !== tab.dataset.tab) {
			navTab.classList.remove(activeTabClass, "underline");
			navTab.setAttribute("aria-selected", "false");
		} else {
			navTab.classList.add(activeTabClass, "underline");
			navTab.setAttribute("aria-selected", "true");
			scrollTo(navTab);
		}
	});
}

let excecute = false;

function hashtagActive(hash, tab) {
	hashtagActive = function () {}; // kill it as soon as it was called
	tab = document.querySelector('[data-hash="' + hash + '"]');

	toggleActiveTab(tab);
	findActiveContent();

	excecute = true;
}

function scrollTo(el) {
	const tabCont = el.closest("[data-tab-navigation]");

	if (tabCont && tabCont.getAttribute("data-orientation") == "horizontal") {
		el.scrollIntoView({
			behavior: "smooth",
			block: "nearest",
			inline: "center",
		});
	}
}

function switchTab(tab) {
	if (window.location.hash && !excecute) {
		hashtagActive(window.location.hash.replace("#", ""), tab);
	} else {
		toggleActiveTab(tab);
		findActiveContent();
	}
}

/**
 * @title Navigation controller
 * @description Based on key input decide what actions should be taken to control
 * menu
 *
 * @param {event} event
 */
function tabNavigation() {
	const dataTab = document.querySelector(".tab");
	if (dataTab) {
		switchTab(dataTab);

		// Click Event
		document.querySelector("body").addEventListener("click", function (e) {
			if (e.target.hasAttribute("data-tab")) {
				switchTab(e.target);
				e.preventDefault();

				if (e.target.dataset.hash) {
					window.history.replaceState(null, null, "#" + e.target.dataset.hash);
				} else {
					window.history.replaceState(null, null, null);
				}
			}
		});
	}
}

// Load all JS after Content Load
document.addEventListener("DOMContentLoaded", function () {
	tabNavigation();
});
