const api_url = "http://localhost:9090/api/";

const loginBtn = document.getElementById("login-btn");
const signupBtn = document.getElementById("signup-btn");
const logoutBtns = document.querySelectorAll(".logout-btn");
const myApplicationsBtn = document.getElementById("my-applications-btn");
const prevPageBtn = document.getElementById("prev-page-btn");
const nextPageBtn = document.getElementById("next-page-btn");
const viewEventApplicationsBtn = document.getElementById(
  "view-event-applications-btn"
);
const viewApplicationsBtn = document.getElementById("view-applications-btn");
const viewApplicationTypesBtn = document.getElementById(
  "view-application-types-btn"
);
const addApplicationTypeBtn = document.getElementById(
  "add-application-type-btn"
);
const addApplicationBtn = document.getElementById("add-application-btn");
const authControls = document.getElementById("auth-controls");
const vendorMenu = document.getElementById("vendor-menu");
const organizerMenu = document.getElementById("organizer-menu");
const welcomeContent = document.getElementById("welcome-content");
const applicationContent = document.getElementById("application-content");
const applicationTypesContent = document.getElementById(
  "application-types-content"
);
const applicationLists = document.getElementById("applications-list");
const applicationTypeLists = document.getElementById("application-types-list");
const closeFormBtn = document.getElementById("close-form-btn");
const closeApplicationFormBtn = document.getElementById(
  "close-application-form-btn"
);
const addApplicationTypeForm = document.getElementById(
  "add-application-type-form"
);
const applicationTypeSelect = document.getElementById(
  "application-type-select"
);
const addApplicationForm = document.getElementById("add-application-form");
const statusFilter = document.getElementById("status-filter");

const applicationTypeFilter = document.getElementById(
  "application-type-filter"
);

let user = {
  isAuthenticated: false,
  user_id: null,
  email: null,
  user_type: null, // or 'vendor' or 'event_organizer'
};

let currentStartingAfter = 0;
let currentStartingBefore = null;

function updateUI() {
  if (!user.isAuthenticated) {
    console.log("auth");
    authControls.classList.remove("hidden");
    vendorMenu.classList.add("hidden");
    organizerMenu.classList.add("hidden");
    welcomeContent.classList.remove("hidden");
    applicationContent.classList.add("hidden");
    applicationTypesContent.classList.add("hidden");
  } else if (user.user_type === "vendor") {
    console.log("vendor");
    authControls.classList.add("hidden");
    vendorMenu.classList.remove("hidden");
    organizerMenu.classList.add("hidden");
    welcomeContent.classList.add("hidden");
    applicationContent.classList.remove("hidden");
    applicationTypesContent.classList.add("hidden");
    addApplicationBtn.classList.remove("hidden");
    addApplicationTypeBtn.classList.add("hidden");
    loadVendorApplications(statusFilter.value);
  } else if (user.user_type === "event_organizer") {
    console.log("event_organize");
    authControls.classList.add("hidden");
    vendorMenu.classList.add("hidden");
    organizerMenu.classList.remove("hidden");
    welcomeContent.classList.add("hidden");
    applicationContent.classList.remove("hidden");
    applicationTypesContent.classList.add("hidden");
    addApplicationBtn.classList.add("hidden");
    addApplicationTypeBtn.classList.remove("hidden");
    loadOrganizerApplications();
  }
}

async function handleLogin(event) {
  event.preventDefault();
  const emailInput = document.getElementById("signin-email-input");
  const passwordInput = document.getElementById("signin-password-input");

  const data = {
    email: emailInput.value,
    password: passwordInput.value,
  };

  try {
    const response = await fetch(`${api_url}users/login`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });
    const result = await response.json();
    if (result.success) {
      console.log("Login successful:", result);
      user.isAuthenticated = true;
      user.user_type = result.user_type;
      user.email = result.email;
      user.user_id = result.user_id;
      updateUI();
    } else {
      console.error("Login failed:", result.message);

      user.isAuthenticated = false;
      user.user_type = null;
      user.email = null;
      user.user_id = null;
      alert("Login failed: " + result.message);
    }
  } catch (error) {
    console.error("Error during login:", error);
    alert("An error occurred during login.");
  }
}

async function handleSignup(event) {
  event.preventDefault();
  const emailInput = document.getElementById("signup-email-input");
  const nameInput = document.getElementById("signup-name-input");
  const passwordInput = document.getElementById("signup-password-input");
  const signupData = {
    name: nameInput.value,
    email: emailInput.value,
    password: passwordInput.value,
    user_type: document.querySelector('input[name="user_type"]:checked').value,
  };

  try {
    const response = await fetch(`${api_url}users`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(signupData),
    });
    const data = await response.json();
    if (data.success) {
      alert("Sign up successful. Logging in...");
      user.isAuthenticated = true;
      user.user_type = data.user_type;
      user.user_id = data.user_id;
      user.email = data.email;
      updateUI();
    } else {
      alert("Sign up failed: " + data.message);
    }
  } catch (error) {
    console.log(error);
  }
}

async function handleLogout() {
  try {
    const res = await fetch(
      `http://localhost:9090/backend/src/view/logout.php`
    );
    console.log(res);
    user.isAuthenticated = false;
    user.user_type = null;
    updateUI();
  } catch (error) {
    alert("Failed to logout", error);
  }
}

async function checkLoginStatus() {
  try {
    const response = await fetch(
      "http://localhost:9090/backend/src/check_login.php"
    );
    const data = await response.json();
    if (data.loggedIn) {
      user.isAuthenticated = true;
      user.user_type = data.user_type;
      user.email = data.email;
      user.user_id = data.user_id;
    } else {
      user.isAuthenticated = false;
      user.user_type = null;
      user.email = null;
      user.user_id = nu;
    }
  } catch (error) {
    console.error("Error checking login status:", error);
  } finally {
    updateUI();
  }
}

function showApplications() {
  applicationContent.classList.remove("hidden");
  applicationTypesContent.classList.add("hidden");
  if (user.user_type === "vendor") {
    loadVendorApplications(statusFilter.value);
  } else {
    loadOrganizerApplications(statusFilter.value);
  }
}

function showApplicationTypes() {
  applicationTypesContent.classList.remove("hidden");
  applicationContent.classList.add("hidden");
  loadApplicationTypes();
}

function addNewApplicationType() {
  addApplicationTypeForm.classList.remove("hidden");
}

// Function to add a new application
async function addNewApplication(selectedApplicationTypeId = null) {
  addApplicationForm.classList.remove("hidden");
  try {
    const response = await fetch(`${api_url}application-types`);
    const data = await response.json();

    if (data && data.length > 0) {
      // Populate the dropdown with application types
      const applicationTypeSelect = document.getElementById(
        "application-type-select"
      );
      applicationTypeSelect.innerHTML = ""; // Clear previous options

      data.forEach((applicationType) => {
        const option = document.createElement("option");
        option.value = applicationType.id;
        option.textContent = applicationType.title;
        applicationTypeSelect.appendChild(option);
      });

      // Set the selected application type as default in the form
      if (selectedApplicationTypeId) {
        const selectedType = data.find(
          (type) => type.id == selectedApplicationTypeId
        );
        setApplicationTypeInForm(selectedType);
        applicationTypeSelect.value = selectedApplicationTypeId;
      } else if (data.length > 0) {
        // If no specific application type is selected, set the first one as default
        setApplicationTypeInForm(data[0]);
        applicationTypeSelect.value = data[0].id;
      }

      // Handle change event when vendor selects a different application type
      applicationTypeSelect.addEventListener("change", function () {
        const selectedTypeId = this.value;
        const selectedType = data.find((type) => type.id == selectedTypeId);
        setApplicationTypeInForm(selectedType);
      });
    } else {
      alert("Failed to load application types.");
    }
  } catch (error) {
    console.error("Error loading application types:", error);
  }
}

async function loadVendorApplications(
  status = "all",
  applicationTypeId = "all",
  isPrevPage = false
) {
  await loadApplications(user.user_id, status, applicationTypeId, isPrevPage);
}

async function handleUpdateStatus(applicationId, status) {
  const confirmation = confirm(
    "Are you sure you want to update this application?"
  );
  if (!confirmation) return;

  try {
    const response = await fetch(`${api_url}applications/${applicationId}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ status: status }),
    });

    const result = await response.json();
    console.log(result);
    if (result.success) {
      alert("Application updated successfully!");
      if (user.user_type === "vendor") {
        loadVendorApplications(statusFilter.value, applicationTypeFilter.value);
      } else {
        loadOrganizerApplications(
          statusFilter.value,
          applicationTypeFilter.value
        );
      }
    } else {
      alert("Failed to update the application: " + result.message);
    }
  } catch (error) {
    console.error("Error updating application:", error);
    alert("An error occurred while trying to updating the application.");
  }
}

async function loadOrganizerApplications(
  status = "all",
  applicationTypeId = "all",
  isPrevPage = false
) {
  await loadApplications(null, status, applicationTypeId, isPrevPage);
}

async function loadApplicationTypes() {
  try {
    const response = await fetch(`${api_url}application-types`);
    const data = await response.json();

    if (data && data.length > 0) {
      const applicationTypesList = document.getElementById(
        "application-types-list"
      );
      applicationTypesList.innerHTML = "";

      // Populate the dropdown with application types

      applicationTypeSelect.innerHTML = "";

      applicationTypeFilter.innerHTML = `<option value="all">All</option>`;

      data.forEach((applicationType) => {
        const card = document.createElement("div");
        card.classList.add("application-type-card");
        card.innerHTML = `
          <img src="${applicationType.cover_photo}" alt="${applicationType.title}">
          <h3>${applicationType.title}</h3>
          <p>${applicationType.description}</p>
          <p>${applicationType.deadline}</p>
        `;

        card.addEventListener("click", () => {
          if (user.user_type === "vendor") {
            addNewApplication(applicationType.id);
          }
        });

        applicationTypesList.appendChild(card);

        const option = document.createElement("option");
        option.value = applicationType.id;
        option.textContent = applicationType.title;
        applicationTypeSelect.appendChild(option);

        const option1 = document.createElement("option");
        option1.value = applicationType.id;
        option1.textContent = applicationType.title;
        applicationTypeFilter.appendChild(option1);
      });

      // Set the first application type as the default in the form
      if (data.length > 0) {
        setApplicationTypeInForm(data[0]);
      }
    } else {
      alert("Failed to load application types.");
    }
  } catch (error) {
    console.error("Error loading application types:", error);
  }
}

async function loadApplications(
  userId = null,
  status = "all",
  applicationTypeId = "all",
  isPrevPage = false
) {
  try {
    let url = `${api_url}applications`;

    // Apply user_id filter if provided
    if (userId) {
      url += `?user_id=${userId}`;
    } else {
      url += `?`;
    }

    // Apply status filter if not "all"
    if (status !== "all") {
      url += `&status=${status}`;
    }

    if (applicationTypeId !== "all") {
      url += `&application_type_id=${applicationTypeId}`;
    }

    // Apply pagination filters
    if (isPrevPage && currentStartingBefore) {
      url += `&starting_before=${currentStartingBefore}`;
    } else if (currentStartingAfter) {
      url += `&starting_after=${currentStartingAfter}`;
    }

    console.log(url);

    const response = await fetch(url);
    const data = await response.json();

    console.log(data);

    // Clear existing list
    applicationLists.innerHTML = "";

    // Create a table element
    const table = document.createElement("table");
    table.classList.add("applications-table");

    // Create the table header
    const thead = document.createElement("thead");
    const headerRow = document.createElement("tr");

    const headers = [
      "Cover Photo",
      "Title",
      "Description",
      "Deadline",
      "Status",
      "Actions",
    ];

    if (user.user_type === "event_organizer") {
      headers.unshift("Email");
      headers.unshift("Name");
    }
    headers.forEach((headerText) => {
      const th = document.createElement("th");
      th.textContent = headerText;
      headerRow.appendChild(th);
    });

    thead.appendChild(headerRow);
    table.appendChild(thead);

    // Create the table body
    const tbody = document.createElement("tbody");

    data.data.forEach((application) => {
      const row = document.createElement("tr");

      if (user.user_type === "event_organizer") {
        const nameCell = document.createElement("td");
        nameCell.textContent = application.user_name;
        row.appendChild(nameCell);

        const emailCell = document.createElement("td");
        emailCell.textContent = application.user_email;
        row.appendChild(emailCell);
      }

      // Cover Photo
      const coverPhotoCell = document.createElement("td");
      const imgElement = document.createElement("img");
      imgElement.src = application.application_type_cover_photo;
      imgElement.alt = application.application_type_title;
      imgElement.classList.add("application-cover-photo");
      coverPhotoCell.appendChild(imgElement);
      row.appendChild(coverPhotoCell);

      // Title
      const titleCell = document.createElement("td");
      titleCell.textContent = application.application_type_title;
      row.appendChild(titleCell);

      // Description
      const descriptionCell = document.createElement("td");
      descriptionCell.textContent = application.application_type_description;
      row.appendChild(descriptionCell);

      // Deadline
      const deadlineCell = document.createElement("td");
      deadlineCell.textContent = application.application_type_deadline;
      row.appendChild(deadlineCell);

      // Status
      const statusCell = document.createElement("td");
      statusCell.textContent = application.status;
      row.appendChild(statusCell);

      // Actions (Withdraw button for vendors)
      const actionsCell = document.createElement("td");
      if (user.user_type === "vendor" && application.status !== "withdrawn") {
        // Only show the withdraw button if the application is not already withdrawn
        const withdrawButton = document.createElement("button");
        withdrawButton.textContent = "Withdraw";
        withdrawButton.classList.add("withdraw-btn");
        withdrawButton.addEventListener("click", () =>
          handleUpdateStatus(application.id, "withdraw")
        );
        actionsCell.appendChild(withdrawButton);
      } else if (application.status === "withdrawn") {
        actionsCell.textContent = "Withdrawn";
      } else if (user.user_type === "event_organizer") {
        const statusSelect = document.createElement("select");
        const statuses = ["pending", "approved", "waitlist", "rejected"];
        statuses.forEach((statusOption) => {
          const option = document.createElement("option");
          option.value = statusOption;
          option.textContent =
            statusOption.charAt(0).toUpperCase() + statusOption.slice(1);
          if (statusOption === application.status) {
            option.selected = true;
          }
          statusSelect.appendChild(option);
        });

        statusSelect.addEventListener("change", () => {
          const newStatus = statusSelect.value;
          handleUpdateStatus(application.id, newStatus);
        });

        actionsCell.appendChild(statusSelect);
      }
      row.appendChild(actionsCell);

      tbody.appendChild(row);
    });

    table.appendChild(tbody);
    applicationLists.appendChild(table);

    // Update pagination cursors
    currentStartingAfter = data.next
      ? data.next.split("starting_after=")[1]
      : null;
    currentStartingBefore = data.previous
      ? data.previous.split("starting_before=")[1]
      : null;

    console.log("currentStartingAfter", currentStartingAfter);
    console.log("currentStartingBefore", currentStartingBefore);

    // Update pagination buttons
    prevPageBtn.disabled = !currentStartingBefore;
    nextPageBtn.disabled = !currentStartingAfter;
  } catch (error) {
    console.error("Error loading applications:", error);
  }
}

// Function to set application type details in the form
function setApplicationTypeInForm(applicationType) {
  document.getElementById("application-type-id").value = applicationType.id;
  document.getElementById("application-title").value = applicationType.title;
  document.getElementById("application-cover-photo").src =
    applicationType.cover_photo;
  document.getElementById("application-description").value =
    applicationType.description;
  document.getElementById("application-deadline").value =
    applicationType.deadline;
}

function handleCloseAddForm() {
  addApplicationTypeForm.classList.add("hidden");
  addApplicationForm.classList.add("hidden");
}

async function handleAddApplicationType(event) {
  event.preventDefault();
  const applicationTypeForm = document.getElementById("application-type-form");

  const formData = new FormData(applicationTypeForm);

  try {
    const response = await fetch(`${api_url}application-types`, {
      method: "POST",
      body: formData,
    });

    const result = await response.json();
    console.log(result);
    if (result.success) {
      alert("Application Type created successfully!");
      handleCloseAddForm();
      applicationTypeForm.reset();
      loadApplicationTypes();
    } else {
      alert("Failed to create Application Type: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
  }
}

async function handleAddApplication(event) {
  event.preventDefault();

  const form = event.target;
  const applicationTypeId = form.querySelector("#application-type-id").value;

  const data = {
    user_id: user.user_id,
    application_type_id: parseInt(applicationTypeId),
  };
  try {
    const response = await fetch(`${api_url}applications`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    const result = await response.json();
    console.log(result);

    if (result.success) {
      alert("Application created successfully!");
      handleCloseAddForm();
    } else {
      alert("Failed to create application: " + result.message);
    }
  } catch (error) {
    console.error("Error:", error);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  loginBtn.addEventListener("click", handleLogin);
  signupBtn.addEventListener("click", handleSignup);
  logoutBtns.forEach((button) => {
    button.addEventListener("click", handleLogout);
  });
  viewApplicationsBtn.addEventListener("click", showApplications);
  viewApplicationTypesBtn.addEventListener("click", showApplicationTypes);
  addApplicationTypeBtn.addEventListener("click", addNewApplicationType);
  closeFormBtn.addEventListener("click", handleCloseAddForm);
  addApplicationTypeForm.addEventListener("submit", handleAddApplicationType);
  addApplicationForm.addEventListener("submit", handleAddApplication);
  viewEventApplicationsBtn.addEventListener("click", showApplicationTypes);
  myApplicationsBtn.addEventListener("click", showApplications);
  addApplicationBtn.addEventListener("click", addNewApplication);
  closeApplicationFormBtn.addEventListener("click", handleCloseAddForm);
  statusFilter.addEventListener("change", function () {
    const selectedStatus = statusFilter.value;
    currentStartingAfter = 0;
    currentStartingBefore = null;

    if (user.user_type === "vendor") {
      loadVendorApplications(selectedStatus, applicationTypeFilter.value);
    } else {
      loadOrganizerApplications(selectedStatus, applicationTypeFilter.value);
    }
  });
  prevPageBtn.addEventListener("click", function () {
    if (currentStartingBefore) {
      if (user.user_type === "vendor") {
        loadVendorApplications(
          statusFilter.value,
          applicationTypeFilter.value,
          true
        );
      } else {
        loadOrganizerApplications(
          statusFilter.value,
          applicationTypeFilter.value,
          true
        );
      }
    }
  });
  nextPageBtn.addEventListener("click", function () {
    if (currentStartingAfter) {
      if (user.user_type === "vendor") {
        loadVendorApplications(statusFilter.value);
      } else {
        loadOrganizerApplications(statusFilter.value);
      }
    }
  });
  // Handle change event when vendor selects a different application type
  applicationTypeSelect.addEventListener("change", function () {
    const selectedTypeId = this.value;
    const selectedType = data.find((type) => type.id == selectedTypeId);
    setApplicationTypeInForm(selectedType);
  });
  applicationTypeFilter.addEventListener("change", function (event) {
    currentStartingAfter = 0;
    currentStartingBefore = null;
    if (user.user_type === "vendor") {
      loadVendorApplications(statusFilter.value, applicationTypeFilter.value);
    } else {
      loadOrganizerApplications(
        statusFilter.value,
        applicationTypeFilter.value
      );
    }
  });
  loadApplicationTypes();
  checkLoginStatus(); // Check login status on page load
  updateUI();
});

let switchCtn = document.querySelector("#switch-cnt");
let switchC1 = document.querySelector("#switch-c1");
let switchC2 = document.querySelector("#switch-c2");
let switchCircle = document.querySelectorAll(".switch_circle");
let switchBtn = document.querySelectorAll(".switch-btn");
let aContainer = document.querySelector("#a-container");
let bContainer = document.querySelector("#b-container");
let allButtons = document.querySelectorAll(".submit");

let getButtons = (e) => e.preventDefault();
let changeForm = (e) => {
  switchCtn.classList.add("is-gx");
  setTimeout(function () {
    switchCtn.classList.remove("is-gx");
  }, 1500);
  switchCtn.classList.toggle("is-txr");
  switchCircle[0].classList.toggle("is-txr");
  switchCircle[1].classList.toggle("is-txr");

  switchC1.classList.toggle("is-hidden");
  switchC2.classList.toggle("is-hidden");
  aContainer.classList.toggle("is-txl");
  bContainer.classList.toggle("is-txl");
  bContainer.classList.toggle("is-z");
};

let shell = (e) => {
  for (var i = 0; i < allButtons.length; i++)
    allButtons[i].addEventListener("click", getButtons);
  for (var i = 0; i < switchBtn.length; i++)
    switchBtn[i].addEventListener("click", changeForm);
};
window.addEventListener("load", shell);
