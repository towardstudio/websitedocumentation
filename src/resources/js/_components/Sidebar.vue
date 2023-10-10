<template>
	<nav id="sidebar-menu">
		<div
			class="nav-item"
			v-for="(section, index) in sections"
			:key="section.title"
		>
			<div
				class="nav-item__title"
				data-parent
				@click="switchSection($event.target)"
				:data-sub="[section.sub ? true : false]"
				:data-section="kebabCase(section.title)"
			>
				<span class="icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 166.4 122.4">
						<path
							d="M104.2,0L83.6,8.7c10.9,23.5,24.9,35.3,43.6,42.9H0V71h126.7
							c-18.2,8.7-32.4,19.4-43.1,42.6l20.6,8.7c22.5-46.2,45.7-50.9,62.3-53.7V53.7C149.9,50.9,126.7,46.2,104.2,0"
						/>
					</svg>
				</span>
				{{ section.title }}
				<span class="chevron" v-if="section.sub">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 90">
						<polygon
							points="49.7,69.7 39.1,69.6 2,30.5 12.7,20.4 44.5,54 77.5,20.3 88,30.6 "
						/>
					</svg>
				</span>
			</div>
			<div :data-submenu="kebabCase(section.title)" v-if="section.sub">
				<div v-for="item in section.sub" :key="item.title">
					<div
						class="nav-item__title nav-item__title--sub"
						@click="switchSection($event.target)"
						:data-sub="[item.sub ? true : false]"
						:data-section="kebabCase(item.title)"
					>
						<span class="icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 166.4 122.4">
								<path
									d="M104.2,0L83.6,8.7c10.9,23.5,24.9,35.3,43.6,42.9H0V71h126.7
									c-18.2,8.7-32.4,19.4-43.1,42.6l20.6,8.7c22.5-46.2,45.7-50.9,62.3-53.7V53.7C149.9,50.9,126.7,46.2,104.2,0"
								/>
							</svg>
						</span>
						{{ item.title }}
						<span class="chevron" v-if="item.sub">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90 90">
								<polygon
									points="49.7,69.7 39.1,69.6 2,30.5 12.7,20.4 44.5,54 77.5,20.3 88,30.6 "
								/>
							</svg>
						</span>
					</div>
					<div
						:data-submenu="kebabCase(section.title)"
						data-child-sub
						v-if="item.sub"
					>
						<div
							class="nav-item__title nav-item__title--child"
							v-for="child in item.sub"
							:key="child.title"
							@click="switchSection($event.target)"
							:data-sub="[child.sub ? true : false]"
							:data-section="kebabCase(item.title) + '-' + kebabCase(child.title)"
						>
							<span class="icon">
								<svg
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 166.4 122.4"
								>
									<path
										d="M104.2,0L83.6,8.7c10.9,23.5,24.9,35.3,43.6,42.9H0V71h126.7
											c-18.2,8.7-32.4,19.4-43.1,42.6l20.6,8.7c22.5-46.2,45.7-50.9,62.3-53.7V53.7C149.9,50.9,126.7,46.2,104.2,0"
									/>
								</svg>
							</span>
							{{ child.title }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</nav>
</template>

<style>
[data-parent].active-section {
	color: v-bind(activeText);
	background-color: v-bind(activeBg);

	.icon {
		display: block;
		fill: v-bind(activeText);
	}

	.chevron {
		fill: v-bind(activeText);
	}
}

.nav-item__title--sub.active-item,
.nav-item__title--child.active-item {
	.icon {
		display: block;
	}
}

.nav-item__title--sub.active-item {
	background-color: v-bind(subActivebg);
}

.nav-item__title--child.active-item {
	background-color: v-bind(childActivebg);
}

.icon {
	display: none;
}
</style>

<script>
export default {
	props: ["sections", "bgColor", "textColor"],
	data: function () {
		return {
			execute: false,
			activeItem: "colors",
			subActive: [],
			activeBg: "#" + this.bgColor,
			activeText: "#" + this.textColor,
			activeClass: "active-section",
			childActive: "active-item",
			subActivebg: "#" + this.bgColor + "80",
			childActivebg: "#" + this.bgColor + "40",
		};
	},
	mounted() {
		let firstItem = document.querySelector("[data-section]");
		if (
			firstItem.hasAttribute("data-sub") &&
			firstItem.dataset.sub === "true"
		) {
			const submenu = firstItem.nextElementSibling;
			firstItem = submenu.querySelector("[data-section]");
		}

		if (window.location.hash && !this.execute) {
			this.switchSection(firstItem);
		} else {
			this.hashtagActive(firstItem.dataset.section, firstItem);
		}
	},
	methods: {
		getSiblings(e) {
			let siblings = [];
			// if no parent, return no sibling
			if (!e.parentNode) {
				return siblings;
			}
			// first child of the parent node
			let sibling = e.parentNode.firstChild;

			// collecting siblings
			while (sibling) {
				if (sibling.nodeType === 1 && sibling !== e) {
					siblings.push(sibling);
				}
				sibling = sibling.nextSibling;
			}
			return siblings;
		},
		switchSection(item) {
			const itemSubExists = item.hasAttribute("data-sub");
			const itemSub = item.dataset.sub;

			const parentPage = item.hasAttribute("data-parent") ? true : false;
			let parent = item.hasAttribute("data-parent")
				? item.dataset.section
				: item.closest("[data-submenu]").dataset.submenu;
			parent = document.querySelector('[data-section="' + parent + '"]');

			if (window.location.hash && !this.execute) {
				const hashtag = window.location.hash.replace("#", "");
				const activeTab = document.querySelector('[data-section="' + item.dataset.section + '"]');

				if (activeTab) {
					this.hashtagActive(window.location.hash.replace("#", ""), item);
				} else {
					this.toggleActiveSection(item);
					this.findActiveContent();
				}
			} else if (itemSubExists) {
				if (itemSub === "false" && parentPage) {
					this.toggleActiveSection(item);
					this.findActiveContent(item);
					window.history.replaceState(null, null, "#" + item.dataset.section);
				} else if (itemSub === "false") {
					this.toggleActiveSection(parent, item);
					this.findActiveContent(item);
					window.history.replaceState(null, null, "#" + item.dataset.section);
				} else {
					this.toggleActiveSection(parent, item);
					const submenu = item.nextElementSibling;
					this.openSubMenu(submenu, item);
				}
			}

			// Fire Event for Change of Section
			const eventAwesome = new CustomEvent("section-change", {
  				bubbles: true,
  				detail: window.location.hash,
			});

			document.dispatchEvent(eventAwesome);

		},
		openSubMenu(submenu, item) {
			if (item && item.hasAttribute("aria-expanded")) {
				if (item.getAttribute("aria-expanded") === "true") {
					this.closeSubMenu([submenu], item);
				} else {
					submenu.classList.add("open");
					item.setAttribute("aria-expanded", "true");
				}
			} else {
				submenu.classList.add("open");
				if (item) {
					item.setAttribute("aria-expanded", "true");
				}
			}
		},
		closeSubMenu(submenus, item) {
			const childClass = this.childActive;
			submenus.forEach(function (submenu) {
				const parentItem = submenu.previousElementSibling;

				if (parentItem && parentItem.hasAttribute("aria-expanded")) {
					if (parentItem.getAttribute("aria-expanded") === "true") {
						parentItem.setAttribute("aria-expanded", "false");
						parentItem.classList.remove(childClass);
					}
				}

				submenu.classList.remove("open");

				const children = submenu.querySelectorAll("." + childClass);
				children.forEach(function (child) {
					child.classList.remove(childClass);
				});
			});
		},
		hashtagActive(hash, section) {
			this.hashtagActive = function () {}; // kill it as soon as it was called
			section = document.querySelector('[data-section="' + hash + '"]');
			let parentSub = section.closest("[data-submenu]");
			const sidebar = document.getElementById("aside");

			if (parentSub) {
				this.openSubMenu(parentSub, parentSub.previousElementSibling);
				const parentItem = document.querySelector(
					'[data-section="' + parentSub.dataset.submenu + '"]',
				);
				this.toggleActiveSection(parentItem, section);

				sidebar.scrollTop = section.offsetTop + 50;

				if (parentSub.hasAttribute("data-child-sub")) {
					this.openSubMenu(parentSub.parentNode.closest("[data-submenu]"));
				}
			} else {
				this.toggleActiveSection(section);
				sidebar.scrollTop = section.offsetTop + 50;
			}

			this.findActiveContent(section);

			this.execute = true;
		},
		toggleActiveSection(section, item) {
			const data = this;
			const activeSectionClass = data.activeClass;
			const activeItemClass = data.childActive;
			let navSections = document.querySelectorAll("[data-section]");
			navSections = Array.from(navSections);

			if (item) {
				navSections.forEach(function (navSection) {
					let parentItem = item.parentNode.closest("[data-submenu]");
					parentItem =
						parentItem && parentItem.hasAttribute("data-child-sub")
							? parentItem.previousElementSibling
							: null;

					if (navSection !== item) {
						navSection.classList.remove(activeItemClass);
					} else {
						navSection.classList.add(activeItemClass);
						if (parentItem) {
							parentItem.classList.add(activeItemClass);
						}
						const siblings = data.getSiblings(navSection.parentNode);
						if (siblings) {
							siblings.forEach(function (sibling) {
								const submenus = sibling.querySelectorAll("[data-submenu]");
								data.closeSubMenu(submenus);
							});
						}
					}
				});
			}

			let navParents = document.querySelectorAll("[data-parent]");
			navParents = Array.from(navParents);

			navParents.forEach(function (navParent) {
				if (navParent !== section) {
					navParent.classList.remove(activeSectionClass);
				} else {
					navParent.classList.add(activeSectionClass);
					const siblings = data.getSiblings(navParent.parentNode);
					if (siblings) {
						siblings.forEach(function (sibling) {
							const submenus = sibling.querySelectorAll("[data-submenu]");
							data.closeSubMenu(submenus);
						});
					}
				}
			});
		},
		findActiveContent(activeSection) {
			const activeSectionClass = this.activeClass;
			const sidebarMenu = document.getElementById("sidebar-menu");

			const activeSections = sidebarMenu.querySelectorAll(
				"." + activeSectionClass,
			);

			if (!activeSections.length) {
				sidebarMenu
					.querySelector("[data-section]")
					.classList.add(activeSectionClass);
			}

			this.showHideContent(sidebarMenu, activeSection);

			const firstActiveSection = document.querySelector("." + this.activeClass);
		},
		showHideContent(sidebarMenu, activeSection) {
			const data = this;
			const activeSectionName = activeSection.dataset.section;
			const activeContentBlock = document.querySelectorAll(
				'[data-content="' + activeSectionName + '"]',
			);

			activeContentBlock.forEach(function (activeBlock) {
				const activeBlocksChildren = activeBlock.parentElement.children;
				const contentBlocks = Array.from(activeBlocksChildren);
				const iframe = activeBlock.querySelector("iframe");

				contentBlocks.forEach(function (contentBlock) {
					if (
						contentBlock === activeBlock &&
						contentBlock.hasAttribute("data-content")
					) {
						contentBlock.classList.add("open");

						if (!iframe.src) {
							iframe.src = iframe.dataset.path;
							iframe.addEventListener("load", data.handleLoad, true);
						}
					} else if (
						contentBlock !== activeBlock &&
						contentBlock.hasAttribute("data-content")
					) {
						contentBlock.classList.remove("open");
					}
				});
			});
		},
		handleLoad(event) {
			const loading = event.target.previousElementSibling;
			loading.remove();
			event.target.classList.add("loaded");
		},
	},
	computed: {
		kebabCase(string) {
			const toKebabCase = (string) =>
				string &&
				string
					.match(
						/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g,
					)
					.map((x) => x.toLowerCase())
					.join("-");

			return toKebabCase;
		},
	},
};
</script>
