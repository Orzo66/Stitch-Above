// Toggle menu function

// Toggle hamburger menu
function toggleMenu() {
	const links = document.getElementById('navLinks');
	if (links) {
		links.classList.toggle('active');
	}
}

// Function to load the external header and footer files
async function loadComponent(id, file) {
	const element = document.getElementById(id);
	if (!element) return;

	try {
		// We use a relative path.
		// If your HTML files are in the root, 'footer.html' is correct.
		const response = await fetch(file);
		if (!response.ok) throw new Error(`Could not find ${file}`);

		const html = await response.text();
		element.innerHTML = html;
	} catch (error) {
		console.warn('Component load failed:', error);
	}
}

// Initialize on Load (tell browser to run these functions as soon as page opens)
window.addEventListener('DOMContentLoaded', () => {
	loadComponent('nav-placeholder', 'header.html');
	loadComponent('footer-placeholder', 'footer.html');
});

// Event Delegation: Listen for clicks on the entire document
document.addEventListener('click', (e) => {
	// If the thing we clicked (or its parent) is the hamburger button
	if (e.target.closest('.hamburger')) {
		toggleMenu();
	}
});

// Function to format the phone number
function formatPhoneNumber(value) {
	if (!value) return value;

	// Strip all non-digit characters
	const phoneNumber = value.replace(/\D/g, '');
	const phoneNumberLength = phoneNumber.length;

	// Format based on length
	if (phoneNumberLength < 4) return phoneNumber;

	if (phoneNumberLength < 7) {
		return `(${phoneNumber.slice(0, 3)}) ${phoneNumber.slice(3)}`;
	}

	return `(${phoneNumber.slice(0, 3)}) ${phoneNumber.slice(3, 6)}-${phoneNumber.slice(6, 10)}`;
}

// Add the listener once the DOM is loaded
window.addEventListener('DOMContentLoaded', () => {
	// ... existing loadComponent calls ...

	const phoneInput = document.getElementById('phone');
	if (phoneInput) {
		phoneInput.addEventListener('input', () => {
			const formattedValue = formatPhoneNumber(phoneInput.value);
			phoneInput.value = formattedValue;
		});
	}
});
