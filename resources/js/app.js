

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('authForm', () => ({
	showPassword: false,
	showConfirmation: false,
	submitting: false,
	password: '',
	submit() {
		if (this.submitting) {
			return;
		}

		this.submitting = true;
	},
	get strengthScore() {
		return [
			this.password.length >= 8,
			/[A-Z]/.test(this.password),
			/[a-z]/.test(this.password),
			/\d/.test(this.password),
			/[^A-Za-z0-9]/.test(this.password),
		].filter(Boolean).length;
	},
	get strengthPercent() {
		return this.password ? this.strengthScore * 20 : 0;
	},
	get strengthLabel() {
		return ['', 'Weak', 'Fair', 'Good', 'Strong', 'Strong'][this.strengthScore] || 'Password strength';
	},
	get strengthClass() {
		return `strength-${this.strengthScore}`;
	},
}));

Alpine.start();
