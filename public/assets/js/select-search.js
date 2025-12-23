/**
 * Select Search Component
 * A searchable dropdown component for better UX
 */

(function () {
    "use strict";

    // Initialize all select-search components on page load
    document.addEventListener("DOMContentLoaded", function () {
        initializeSelectSearch();
    });

    function initializeSelectSearch() {
        const containers = document.querySelectorAll(
            ".select-search-container"
        );

        containers.forEach((container) => {
            const selectId = container.getAttribute("data-select-id");
            if (!selectId) return;

            const hiddenInput = document.getElementById(selectId);
            const display = document.getElementById(selectId + "_display");
            const dropdown = document.getElementById(selectId + "_dropdown");
            const searchInput = document.getElementById(selectId + "_search");
            const optionsContainer = document.getElementById(
                selectId + "_options"
            );
            const otherWrapper = document.getElementById(
                selectId + "_other_wrapper"
            );
            const otherInput = otherWrapper
                ? otherWrapper.querySelector('input[type="text"]')
                : null;

            if (
                !hiddenInput ||
                !display ||
                !dropdown ||
                !searchInput ||
                !optionsContainer
            ) {
                return;
            }

            const selectSearch = new SelectSearchComponent({
                selectId,
                hiddenInput,
                display,
                dropdown,
                searchInput,
                optionsContainer,
                otherWrapper,
                otherInput,
            });

            selectSearch.init();
        });
    }

    class SelectSearchComponent {
        constructor(config) {
            this.selectId = config.selectId;
            this.hiddenInput = config.hiddenInput;
            this.display = config.display;
            this.dropdown = config.dropdown;
            this.searchInput = config.searchInput;
            this.optionsContainer = config.optionsContainer;
            this.otherWrapper = config.otherWrapper;
            this.otherInput = config.otherInput;
            this.options = Array.from(
                this.optionsContainer.querySelectorAll(".select-search-option")
            );
            this.isOpen = false;
            this.selectedOption = null;
            this.highlightedIndex = -1;
            this.filteredOptions = [...this.options];

            // Bind methods
            this.handleDisplayClick = this.handleDisplayClick.bind(this);
            this.handleSearchInput = this.handleSearchInput.bind(this);
            this.handleOptionClick = this.handleOptionClick.bind(this);
            this.handleDocumentClick = this.handleDocumentClick.bind(this);
            this.handleKeyDown = this.handleKeyDown.bind(this);
        }

        init() {
            // Set initial selected value
            this.setInitialValue();

            // Add event listeners
            this.display.addEventListener("click", this.handleDisplayClick);
            this.searchInput.addEventListener("input", this.handleSearchInput);
            this.searchInput.addEventListener("keydown", this.handleKeyDown);
            this.optionsContainer.addEventListener(
                "click",
                this.handleOptionClick
            );
            document.addEventListener("click", this.handleDocumentClick);

            // Handle form submission
            const form = this.hiddenInput.closest("form");
            if (form) {
                form.addEventListener(
                    "submit",
                    this.handleFormSubmit.bind(this)
                );
            }
        }

        setInitialValue() {
            const value = this.hiddenInput.value;
            if (value) {
                const option = this.options.find(
                    (opt) => opt.getAttribute("data-value") === value
                );
                if (option) {
                    // Mark as selected visually in original options
                    option.classList.add("selected");
                    this.selectedOption = option;

                    // Update display
                    const label = option.getAttribute("data-label");
                    const text = this.display.querySelector(
                        ".select-search-text"
                    );
                    text.textContent = label;
                    text.classList.remove("placeholder");

                    // Check if it's the "other" option
                    const isOther =
                        option.getAttribute("data-is-other") === "true";
                    if (isOther && this.otherWrapper) {
                        this.otherWrapper.style.display = "block";
                        if (this.otherInput) {
                            this.otherInput.setAttribute(
                                "required",
                                "required"
                            );
                        }
                    }
                } else {
                    // Value might be a custom "other" value (not in options)
                    // This happens when form was submitted with a custom value
                    const text = this.display.querySelector(
                        ".select-search-text"
                    );
                    text.textContent = value;
                    text.classList.remove("placeholder");

                    // Show other input if it exists
                    if (this.otherWrapper) {
                        this.otherWrapper.style.display = "block";
                        if (this.otherInput) {
                            this.otherInput.setAttribute(
                                "required",
                                "required"
                            );
                            // Set the value if it's different from the hidden input
                            if (
                                !this.otherInput.value ||
                                this.otherInput.value !== value
                            ) {
                                this.otherInput.value = value;
                            }
                        }
                    }
                }
            }
        }

        handleDisplayClick(e) {
            e.stopPropagation();
            this.toggleDropdown();
        }

        toggleDropdown() {
            if (this.isOpen) {
                this.closeDropdown();
            } else {
                this.openDropdown();
            }
        }

        openDropdown() {
            this.isOpen = true;
            this.display.classList.add("active");
            this.dropdown.style.display = "block";
            this.searchInput.focus();
            this.updateFilteredOptions();

            // Scroll to selected option if exists
            if (this.selectedOption) {
                this.selectedOption.scrollIntoView({ block: "nearest" });
            }
        }

        closeDropdown() {
            this.isOpen = false;
            this.display.classList.remove("active");
            this.dropdown.style.display = "none";
            this.searchInput.value = "";
            this.highlightedIndex = -1;
            this.updateFilteredOptions();
        }

        handleSearchInput(e) {
            const query = e.target.value.toLowerCase().trim();
            this.filterOptions(query);
        }

        filterOptions(query) {
            this.filteredOptions = this.options.filter((option) => {
                if (query === "") return true;
                const label = option.getAttribute("data-label").toLowerCase();
                return label.includes(query);
            });

            this.renderOptions();
        }

        updateFilteredOptions() {
            const query = this.searchInput.value.toLowerCase().trim();
            this.filterOptions(query);
        }

        renderOptions() {
            // Clear existing options
            this.optionsContainer.innerHTML = "";

            if (this.filteredOptions.length === 0) {
                const noResults = document.createElement("div");
                noResults.className = "select-search-option no-results";
                noResults.textContent = "No results found";
                this.optionsContainer.appendChild(noResults);
                return;
            }

            // Render filtered options
            this.filteredOptions.forEach((option) => {
                const clonedOption = option.cloneNode(true);
                this.optionsContainer.appendChild(clonedOption);
            });

            // Update highlighted option
            if (
                this.highlightedIndex >= 0 &&
                this.highlightedIndex < this.filteredOptions.length
            ) {
                const highlightedOption =
                    this.optionsContainer.children[this.highlightedIndex];
                if (highlightedOption) {
                    highlightedOption.classList.add("highlighted");
                }
            }
        }

        handleOptionClick(e) {
            const option = e.target.closest(".select-search-option");
            if (!option || option.classList.contains("no-results")) return;

            this.selectOption(option);
        }

        selectOption(optionElement) {
            // Remove previous selection
            if (this.selectedOption) {
                this.selectedOption.classList.remove("selected");
            }

            // Find original option in options array
            const value = optionElement.getAttribute("data-value");
            const label = optionElement.getAttribute("data-label");
            const isOther =
                optionElement.getAttribute("data-is-other") === "true";

            // Set selected option
            this.selectedOption = this.options.find(
                (opt) => opt.getAttribute("data-value") === value
            );

            // Update display
            const text = this.display.querySelector(".select-search-text");
            text.textContent = label;
            text.classList.remove("placeholder");

            // Update hidden input
            this.hiddenInput.value = value;

            // Handle "other" option
            if (isOther && this.otherWrapper) {
                this.otherWrapper.style.display = "block";
                if (this.otherInput) {
                    this.otherInput.setAttribute("required", "required");
                    this.otherInput.focus();
                }
            } else {
                if (this.otherWrapper) {
                    this.otherWrapper.style.display = "none";
                    if (this.otherInput) {
                        this.otherInput.removeAttribute("required");
                        this.otherInput.value = "";
                    }
                }
            }

            // Mark as selected
            this.options.forEach((opt) => {
                if (opt.getAttribute("data-value") === value) {
                    opt.classList.add("selected");
                } else {
                    opt.classList.remove("selected");
                }
            });

            // Close dropdown
            this.closeDropdown();

            // Trigger change event
            this.hiddenInput.dispatchEvent(
                new Event("change", { bubbles: true })
            );
        }

        handleKeyDown(e) {
            const visibleOptions = Array.from(
                this.optionsContainer.querySelectorAll(
                    ".select-search-option:not(.no-results)"
                )
            );

            switch (e.key) {
                case "ArrowDown":
                    e.preventDefault();
                    this.highlightedIndex = Math.min(
                        this.highlightedIndex + 1,
                        visibleOptions.length - 1
                    );
                    this.updateHighlight(visibleOptions);
                    break;
                case "ArrowUp":
                    e.preventDefault();
                    this.highlightedIndex = Math.max(
                        this.highlightedIndex - 1,
                        -1
                    );
                    this.updateHighlight(visibleOptions);
                    break;
                case "Enter":
                    e.preventDefault();
                    if (
                        this.highlightedIndex >= 0 &&
                        this.highlightedIndex < visibleOptions.length
                    ) {
                        visibleOptions[this.highlightedIndex].click();
                    }
                    break;
                case "Escape":
                    e.preventDefault();
                    this.closeDropdown();
                    break;
            }
        }

        updateHighlight(visibleOptions) {
            visibleOptions.forEach((option, index) => {
                if (index === this.highlightedIndex) {
                    option.classList.add("highlighted");
                    option.scrollIntoView({ block: "nearest" });
                } else {
                    option.classList.remove("highlighted");
                }
            });
        }

        handleDocumentClick(e) {
            if (
                !this.dropdown.contains(e.target) &&
                !this.display.contains(e.target)
            ) {
                this.closeDropdown();
            }
        }

        handleFormSubmit(e) {
            // If "Others" is selected, use the other input value
            const selectedValue = this.hiddenInput.value;
            const isOtherOption = this.options.find(
                (opt) =>
                    opt.getAttribute("data-value") === selectedValue &&
                    opt.getAttribute("data-is-other") === "true"
            );

            if (isOtherOption && this.otherInput) {
                const otherValue = this.otherInput.value.trim();
                if (!otherValue) {
                    e.preventDefault();
                    alert(
                        "Please specify your " +
                            (this.display.previousElementSibling?.textContent
                                ?.replace("*", "")
                                .trim() || "value") +
                            "."
                    );
                    if (this.otherInput) {
                        this.otherInput.focus();
                    }
                    return false;
                }
                // Set the hidden input to the custom value
                this.hiddenInput.value = otherValue;
            }
        }

        destroy() {
            this.display.removeEventListener("click", this.handleDisplayClick);
            this.searchInput.removeEventListener(
                "input",
                this.handleSearchInput
            );
            this.searchInput.removeEventListener("keydown", this.handleKeyDown);
            this.optionsContainer.removeEventListener(
                "click",
                this.handleOptionClick
            );
            document.removeEventListener("click", this.handleDocumentClick);
        }
    }

    // Export for global access if needed
    window.SelectSearchComponent = SelectSearchComponent;
})();
