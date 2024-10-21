// Attach event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Initial setup for attaching listeners to the first skill group and other sections
    attachEventListenersForSkills(0);
    document.getElementById('add-education').addEventListener('click', addEducationSection);
    document.getElementById('add-skill-group').addEventListener('click', addSkillGroupSection);
    document.getElementById('add-experience').addEventListener('click', addExperienceSection);

    document.querySelectorAll('.icon-select').forEach(select => {
        select.addEventListener('change', function () {
            updateIconPreview(select);
        });

        // Trigger the update for existing selections on page load
        updateIconPreview(select);
    });

    // Attach listeners for existing bullet points (achievements)
    document.querySelectorAll('.add-bullet').forEach(button => {
        button.addEventListener('click', function () {
            const index = button.getAttribute('data-index');
            addBulletPoint(index);
        });
    });

    // Attach listeners for removing existing bullet points
    document.querySelectorAll('.remove-bullet').forEach(button => {
        button.addEventListener('click', removeBulletPoint);
    });
});

// Function to attach remove event listener for skill groups
function attachRemoveSkillGroupListener(skillGroupsContainer) {
    skillGroupsContainer.querySelectorAll('.remove-skill-group').forEach(button => {
        button.removeEventListener('click', removeSkillGroup);
        button.addEventListener('click', removeSkillGroup);
    });
}

// Function to remove a skill group
function removeSkillGroup(event) {
    event.target.closest('.skill-group').remove();
}

// Function to remove a list item
function removeListItem(event) {
    event.target.closest('li').remove();
}

// Function to add a new education section
function addEducationSection() {
    const educationList = document.getElementById('education-list');
    const newEducation = `
    <li>
    <label for="school_name_${educationList.children.length}">School Name:</label>
    <input type="text" name="school_name[]" id="school_name_${educationList.children.length}" placeholder="University XYZ"><br>
            
    <label for="degree_${educationList.children.length}">Degree:</label>
    <input type="text" name="degree[]" id="degree_${educationList.children.length}" placeholder="Bachelor of Science"><br>
            
    <label for="education_start_date_${educationList.children.length}">Start Date:</label>
    <input type="date" name="education_start_date[]" id="education_start_date_${educationList.children.length}"><br>
            
    <label for="education_end_date_${educationList.children.length}">End Date:</label>
    <input type="date" name="education_end_date[]" id="education_end_date_${educationList.children.length}"><br>
            
    <button type="button" class="remove-item">Remove</button>
    </li>`;
    educationList.insertAdjacentHTML('beforeend', newEducation);
    attachRemoveItemListeners(educationList);
}

// Function to add a new skill group section
function addSkillGroupSection() {
    const skillGroupsContainer = document.getElementById('skill-groups');
    const groupIndex = skillGroupsContainer.children.length;

    const newSkillGroup = `
        <div class="skill-group" data-group-index="${groupIndex}">
            <h4>Skill Group: <input type="text" name="skill_groups[${groupIndex}][name]" placeholder="e.g., Programming Languages"></h4>
            <ul id="skill-list-${groupIndex}">
                <li>
                    <label for="skill_${groupIndex}_0">Skill:</label>
                    <input type="text" name="skill_groups[${groupIndex}][skills][0][name]" id="skill_${groupIndex}_0" placeholder="JavaScript"><br>
                    
                    <label for="icon_${groupIndex}_0">Icon:</label>
                    <select name="skill_groups[${groupIndex}][skills][0][icon]" id="icon_${groupIndex}_0" class="icon-select" data-group-index="${groupIndex}" data-skill-index="0">
                        <option value="fas fa-code">Code Icon</option>
                        <option value="fas fa-user">Person Icon</option>
                        <option value="fas fa-laptop-code">Laptop Code Icon</option>
                        <option value="custom">Upload Custom</option>
                    </select>
                    <i id="icon-preview_${groupIndex}_0" class="fas fa-code icon-preview" style="font-size: 24px; margin-left: 10px;"></i>
                    
                    <button type="button" class="upload-icon" data-group-index="${groupIndex}" data-skill-index="0" style="display:none;">Upload Custom Icon</button>
                    <input type="hidden" name="skill_groups[${groupIndex}][skills][0][custom_icon]" id="custom_icon_${groupIndex}_0">
                    
                    <button type="button" class="remove-skill-item">Remove Skill</button>
                </li>
            </ul>
            <button type="button" class="add-skill" data-group-index="${groupIndex}">Add Skill</button>
            <button type="button" class="remove-skill-group">Remove Skill Group</button>
        </div>`;

    skillGroupsContainer.insertAdjacentHTML('beforeend', newSkillGroup);
    attachEventListenersForSkills(groupIndex);
    attachRemoveSkillGroupListener(skillGroupsContainer);
}

// Function to add a new skill to the group
function addSkillToGroup(groupIndex) {
    const skillList = document.getElementById(`skill-list-${groupIndex}`);
    const skillIndex = skillList.children.length;

    const newSkill = `
        <li>
            <label for="skill_${groupIndex}_${skillIndex}">Skill:</label>
            <input type="text" name="skill_groups[${groupIndex}][skills][${skillIndex}][name]" id="skill_${groupIndex}_${skillIndex}" placeholder="New Skill"><br>
            
            <label for="icon_${groupIndex}_${skillIndex}">Icon:</label>
            <select name="skill_groups[${groupIndex}][skills][${skillIndex}][icon]" id="icon_${groupIndex}_${skillIndex}" class="icon-select" data-group-index="${groupIndex}" data-skill-index="${skillIndex}">
                <option value="fas fa-code">Code Icon</option>
                <option value="fas fa-user">Person Icon</option>
                <option value="fas fa-laptop-code">Laptop Code Icon</option>
                <option value="custom">Upload Custom</option>
            </select>
            <i id="icon-preview_${groupIndex}_${skillIndex}" class="fas fa-code icon-preview" style="font-size: 24px; margin-left: 10px;"></i>
            
            <button type="button" class="upload-icon" data-group-index="${groupIndex}" data-skill-index="${skillIndex}" style="display:none;">Upload Custom Icon</button>
            <input type="hidden" name="skill_groups[${groupIndex}][skills][${skillIndex}][custom_icon]" id="custom_icon_${groupIndex}_${skillIndex}">
            
            <button type="button" class="remove-skill-item">Remove Skill</button>
        </li>`;

    skillList.insertAdjacentHTML('beforeend', newSkill);
    attachRemoveSkillListener();
}

// Function to update icon preview when selected from the dropdown
function updateIconPreview(select) {
    const selectedOption = select.options[select.selectedIndex];
    const iconClass = selectedOption.value;
    const groupIndex = select.dataset.groupIndex;
    const skillIndex = select.dataset.skillIndex;
    const previewElement = document.getElementById(`icon-preview_${groupIndex}_${skillIndex}`);
    const uploadButton = document.querySelector(`button.upload-icon[data-group-index="${groupIndex}"][data-skill-index="${skillIndex}"]`);

    if (iconClass === 'custom') {
        uploadButton.style.display = 'inline-block';
        previewElement.className = '';
        previewElement.style.display = 'none';
    } else {
        uploadButton.style.display = 'none';
        previewElement.className = `${iconClass} icon-preview`;
        previewElement.style.display = 'inline-block';
    }
}

// Function to handle custom icon uploads
function openMediaUploader(event) {
    const button = event.target;
    const groupIndex = button.dataset.groupIndex;
    const skillIndex = button.dataset.skillIndex;

    let mediaUploader = wp.media({
        title: 'Choose Custom Icon',
        button: { text: 'Use This Icon' },
        multiple: false
    });

    mediaUploader.on('select', function () {
        const attachment = mediaUploader.state().get('selection').first().toJSON();
        document.getElementById(`custom_icon_${groupIndex}_${skillIndex}`).value = attachment.url;
        const previewElement = document.getElementById(`icon-preview_${groupIndex}_${skillIndex}`);
        previewElement.src = attachment.url;
        previewElement.className = '';
        previewElement.style.display = 'inline-block';
    });

    mediaUploader.open();
}

// Function to add experience including experience roles
function addBulletPoint(index) {
    const achievementList = document.getElementById(`achievement-list-${index}`);
    const newBullet = `
        <li>
            <input type="text" name="experience_roles[${index}][]" placeholder="Your role or achievement...">
            <button type="button" class="remove-bullet">Remove</button>
        </li>`;
    achievementList.insertAdjacentHTML('beforeend', newBullet);

    // Reattach event listener for the new remove button
    achievementList.querySelectorAll('.remove-bullet').forEach(button => {
        button.removeEventListener('click', removeBulletPoint);
        button.addEventListener('click', removeBulletPoint);
    });
}

// Function to remove a bullet point
function removeBulletPoint(event) {
    event.target.closest('li').remove();
}

// Function to add a new experience section
function addExperienceSection() {
    const experienceList = document.getElementById('experience-list');
    const experienceIndex = experienceList.children.length;

    const newExperience = `
        <li>
            <label for="job_title_${experienceIndex}">Job Title:</label>
            <input type="text" name="job_title[]" id="job_title_${experienceIndex}" placeholder="Job Title"><br>

            <label for="company_name_${experienceIndex}">Company Name:</label>
            <input type="text" name="company_name[]" id="company_name_${experienceIndex}" placeholder="Company Name"><br>
            
            <label for="experience_start_date_${experienceIndex}">Start Date:</label>
            <input type="text" name="experience_start_date[]" id="experience_start_date_${experienceIndex}"><br>
            
            <label for="experience_end_date_${experienceIndex}">End Date:</label>
            <input type="text" name="experience_end_date[]" id="experience_end_date_${experienceIndex}"><br>
            
            <label for="experience_description_${experienceIndex}">Job Description:</label>
            <textarea name="experience_description[]" id="experience_description_${experienceIndex}" rows="4" placeholder="Job Description"></textarea><br>

            <label>Experience Roles:</label>
            <ul id="achievement-list-${experienceIndex}" class="achievement-list"></ul>
            <button type="button" class="add-bullet" data-index="${experienceIndex}">Add Achievement</button><br><br>
            
            <button type="button" class="remove-item">Remove</button>
        </li>`;

    experienceList.insertAdjacentHTML('beforeend', newExperience);

    // Attach the event listener for the bullet point addition to the newly created section
    document.querySelector(`.add-bullet[data-index="${experienceIndex}"]`).addEventListener('click', function () {
        addBulletPoint(experienceIndex);
    });

    // Attach remove item event listener
    attachRemoveItemListeners(experienceList);
}

// Attach remove item listeners
function attachRemoveItemListeners(listElement) {
    listElement.querySelectorAll('.remove-item').forEach(button => {
        button.removeEventListener('click', removeListItem);
        button.addEventListener('click', removeListItem);
    });
}

// Attach event listeners for skills
function attachEventListenersForSkills(groupIndex) {
    document.querySelector(`.add-skill[data-group-index="${groupIndex}"]`).addEventListener('click', function () {
        addSkillToGroup(groupIndex);
    });

    document.querySelectorAll(`#skill-list-${groupIndex} .icon-select`).forEach(select => {
        select.addEventListener('change', function () {
            updateIconPreview(select);
        });
    });

    document.querySelectorAll('.upload-icon').forEach(button => {
        button.removeEventListener('click', openMediaUploader);
        button.addEventListener('click', openMediaUploader);
    });

    attachRemoveSkillListener();
}

// Attach remove event listeners for skills
function attachRemoveSkillListener() {
    document.querySelectorAll('.remove-skill-item').forEach(button => {
        button.removeEventListener('click', removeListItem);
        button.addEventListener('click', removeListItem);
    });
}
