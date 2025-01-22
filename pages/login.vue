<template>
    <main :class="{ 'sign-up-mode': isSignUpMode }">
      <div class="box">
        <div class="inner-box">
          <div class="forms-wrap">
            <!-- Sign-In Form -->
            <form v-if="!showForgotPasswordForm && !showVerificationInput && !showResetPasswordForm" 
                  @submit.prevent="handleLogin" 
                  autocomplete="off" 
                  class="sign-in-form">
              <div class="logo">
                <img src="~assets/images/pc1.png" alt="easyclass" />
                <h4>Snap Sell</h4>
              </div>
              <div class="heading">
                <h2>Welcome Back</h2>
                <h6>Not registered yet?</h6>
                <a href="#" class="toggle" @click.prevent="toggleMode">Sign up</a>
              </div>
              <div class="actual-form">
                <div class="input-wrap">
                  <input
                    type="email"
                    class="input-field"
                    :class="{ active: signInFields[0].active }"
                    v-model="signInFields[0].value"
                    @focus="setActive(0, 'signIn')"
                    @blur="removeActive(0, 'signIn')"
                    required
                  />
                  <label :class="{ active: signInFields[0].active || signInFields[0].value }">Email</label>
                </div>
                <div class="input-wrap">
                  <input
                    type="password"
                    class="input-field"
                    :class="{ active: signInFields[1].active }"
                    v-model="signInFields[1].value"
                    @focus="setActive(1, 'signIn')"
                    @blur="removeActive(1, 'signIn')"
                    required
                  />
                  <label :class="{ active: signInFields[1].active || signInFields[1].value }">Password</label>
                </div>
                <button type="submit" class="sign-btn">Sign In</button>
                <p class="text">
                  Forgotten your password?
                  <a href="#" @click.prevent="showForgotPassword">Get help</a>
                </p>
              </div>
            </form>
  
            <!-- Sign-Up Form -->
            <form @submit.prevent="handleSignup" autocomplete="off" class="sign-up-form">
              <div class="logo">
                <img src="~assets/images/pc1.png" alt="easyclass" />
                <h4>Snap Sell</h4>
              </div>
              <div class="heading">
                <h2>Get Started</h2>
                <h6>Already have an account?</h6>
                <a href="#" class="toggle" @click.prevent="toggleMode">Sign in</a>
              </div>
              <div class="actual-form">
                <div class="inputs-container">
                  <div class="input-wrap" v-for="(field, index) in signUpFields" :key="index">
                    <input
                      :type="field.type"
                      :minlength="field.type === 'password' ? 8 : 4"
                      class="input-field"
                      :class="{ active: field.active }"
                      v-model="field.value"
                      @focus="setActive(index, 'signUp')"
                      @blur="removeActive(index, 'signUp')"
                      required
                    />
                    <label :class="{ active: field.active || field.value }">{{ field.label }}</label>
                  </div>
                </div>

                <button 
                  type="submit" 
                  class="sign-btn"
                  :disabled="!isPasswordValid || !signUpFields[0].value || !signUpFields[1].value"
                >
                  Sign Up
                </button>

                <!-- Move password requirements below button -->
                <div v-if="signUpFields[2]?.type === 'password'" class="password-requirements-container">
                  <div class="password-requirements">
                    <p :class="{ valid: hasMinLength }">
                      <i class="fas" :class="hasMinLength ? 'fa-check' : 'fa-times'"></i>
                      8+ characters
                    </p>
                    <p :class="{ valid: hasUpperCase }">
                      <i class="fas" :class="hasUpperCase ? 'fa-check' : 'fa-times'"></i>
                      Uppercase
                    </p>
                    <p :class="{ valid: hasLowerCase }">
                      <i class="fas" :class="hasLowerCase ? 'fa-check' : 'fa-times'"></i>
                      Lowercase
                    </p>
                    <p :class="{ valid: hasNumber }">
                      <i class="fas" :class="hasNumber ? 'fa-check' : 'fa-times'"></i>
                      Number
                    </p>
                    <p :class="{ valid: hasSpecialChar }">
                      <i class="fas" :class="hasSpecialChar ? 'fa-check' : 'fa-times'"></i>
                      Special char
                    </p>
                  </div>
                </div>
              </div>
            </form>

            <!-- Forgot Password Form -->
            <form v-if="showForgotPasswordForm && !showVerificationInput && !showResetPasswordForm" 
                  @submit.prevent="handleForgotPassword" 
                  class="sign-in-form">
              <div class="logo">
                <img src="~assets/images/pc1.png" alt="easyclass" />
                <h4>Snap Sell</h4>
              </div>
              <div class="heading">
                <h2>Reset Password</h2>
                <h6>Remember your password? <a href="#" class="toggle" @click.prevent="showForgotPasswordForm = false">Sign in</a></h6>
              </div>
              <div class="actual-form">
                <div class="input-wrap">
                  <input
                    type="email"
                    class="input-field"
                    v-model="forgotPasswordEmail"
                    required
                  />
                  <label :class="{ active: forgotPasswordEmail }">Email</label>
                </div>
                <button type="submit" class="sign-btn">Send Reset Code</button>
              </div>
            </form>

            <!-- OTP Verification Form -->
            <form v-if="showVerificationInput" 
                  @submit.prevent="handleVerifyOtp" 
                  class="sign-in-form">
              <div class="logo">
                <img src="~assets/images/pc1.png" alt="easyclass" />
                <h4>Snap Sell</h4>
              </div>
              <div class="heading">
                <h2>Verify Code</h2>
                <h6>{{ isPasswordReset ? 'Enter the code sent to reset your password' : 'Enter the verification code sent to your email' }}</h6>
                <p class="text-sm text-gray-600">
                  Code sent to: {{ isPasswordReset ? forgotPasswordEmail : signInFields[0].value }}
                </p>
              </div>
              <div class="actual-form">
                <div class="input-wrap">
                  <input
                    type="text"
                    class="input-field"
                    v-model="verificationCode"
                    maxlength="6"
                    pattern="[0-9]*"
                    inputmode="numeric"
                    required
                  />
                  <label :class="{ active: verificationCode }">Verification Code</label>
                </div>
                <button type="submit" class="sign-btn" :disabled="!verificationCode">
                  Verify Code
                </button>
                <p class="text">
                  Didn't receive the code?
                  <a href="#" @click.prevent="resendCode">Resend code</a>
                </p>
              </div>
            </form>

            <!-- Reset Password Form -->
            <form v-if="showResetPasswordForm" 
                  @submit.prevent="handleResetPassword" 
                  class="sign-in-form">
              <div class="logo">
                <img src="~assets/images/pc1.png" alt="easyclass" />
                <h4>Snap Sell</h4>
              </div>
              <div class="heading">
                <h2>Set New Password</h2>
              </div>
              <div class="actual-form">
                <div class="input-wrap">
                  <input
                    type="password"
                    class="input-field"
                    v-model="newPassword"
                    required
                  />
                  <label :class="{ active: newPassword }">New Password</label>
                </div>
                <div class="input-wrap">
                  <input
                    type="password"
                    class="input-field"
                    v-model="confirmPassword"
                    required
                  />
                  <label :class="{ active: confirmPassword }">Confirm Password</label>
                </div>
                <button type="submit" class="sign-btn" :disabled="!newPassword || newPassword !== confirmPassword">
                  Reset Password
                </button>
              </div>
            </form>
          </div>
  
          <!-- Carousel Section -->
          <div class="carousel">
            <div class="images-wrapper">
              <img v-for="(img, index) in images" :key="index" :src="img.src" :alt="img.alt" :class="['image', `img-${index + 1}`, { show: index === activeImageIndex }]" />
            </div>
            <div class="text-slider">
              <div class="text-wrap">
                <div class="text-group">
                  <h2 v-for="(text, index) in textSliderContent" :key="index">{{ text }}</h2>
                </div>
              </div>
              <div class="bullets">
                <span
                  v-for="(bullet, index) in images"
                  :key="index"
                  :class="{ active: index === activeImageIndex }"
                  @click="moveSlider(index)"
                ></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </template>
  
  <script setup>
  import { ref, watch, computed, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  import { useToast } from 'vue-toastification'

  const isSignUpMode = ref(false);
  const activeImageIndex = ref(0);
  const showForgotPasswordForm = ref(false);
  const showVerificationInput = ref(false);
  const isPasswordReset = ref(false);
  const showResetPasswordForm = ref(false);
  const forgotPasswordEmail = ref('');
  const verificationCode = ref('');
  const newPassword = ref('');
  const confirmPassword = ref('');

  // Add these refs at the top of your script
  const email = ref('');

    const passwordsMatch = computed(() => 
  newPassword.value === confirmPassword.value && newPassword.value !== ''
);

  const canResetPassword = computed(() => 
  hasMinLength.value && 
  hasUpperCase.value && 
  hasLowerCase.value && 
  hasNumber.value && 
  hasSpecialChar.value && 
  passwordsMatch.value
);
  
  // Define form fields
  const signInFields = ref([
    { type: 'email', value: '', label: 'Email', active: false },
    { type: 'password', value: '', label: 'Password', active: false }
  ]);
  
  const signUpFields = ref([
    { label: 'Name', type: 'text', value: '', active: false },
    { label: 'Email', type: 'email', value: '', active: false },
    { label: 'Password', type: 'password', value: '', active: false }
  ]);

  // Add these computed properties for password validation
  const password = computed(() => signUpFields.value[2]?.value || '');

  const hasMinLength = computed(() => password.value.length >= 8);
  const hasUpperCase = computed(() => /[A-Z]/.test(password.value));
  const hasLowerCase = computed(() => /[a-z]/.test(password.value));
  const hasNumber = computed(() => /\d/.test(password.value));
  const hasSpecialChar = computed(() => /[!@#$%^&*(),.?":{}|<>]/.test(password.value));

  // Add computed property for overall password validity
  const isPasswordValid = computed(() => 
    hasMinLength.value && 
    hasUpperCase.value && 
    hasLowerCase.value && 
    hasNumber.value && 
    hasSpecialChar.value
  );
  
  // Carousel and text slider data
  const images = ref([
    { src: '/images/1st.jpg', alt: 'Image 1' },
    { src: '/images/2nd.jpg', alt: 'Image 2' },
    { src: '/images/portrait.jpg', alt: 'Image 3' }
  ]);
  
  const textSliderContent = ref([
    'Create your own Gallaries',
    'Monetise your Creations',
    'Invite friends to your Gallery'
  ]);
  
  // Toggle between sign-in and sign-up mode
  const toggleMode = () => {
    isSignUpMode.value = !isSignUpMode.value;
  };
  
  // Activate and deactivate input fields
  const setActive = (index, formType) => {
    const field = formType === 'signIn' ? signInFields.value[index] : signUpFields.value[index];
    field.active = true;
  };
  
  const removeActive = (index, formType) => {
    const field = formType === 'signIn' ? signInFields.value[index] : signUpFields.value[index];
    if (!field.value) field.active = false;
  };
  
  // Move the slider and update the active image index
  const moveSlider = (index) => {
    activeImageIndex.value = index;
  
    // Update text slider position
    const textSlider = document.querySelector('.text-group');
    if (textSlider) {
      textSlider.style.transform = `translateY(${-index * 2.2}rem)`;
    }
  };

  const route = useRoute();

  // Watch for route changes
  watch(
    () => route.query.mode,
    (newMode) => {
      isSignUpMode.value = newMode === 'signup';
    },
    { immediate: true }
  );

  const toast = useToast()

  const handleSignup = async () => {
    try {
      if (!isPasswordValid.value) {
        toast.error("Please meet all password requirements");
        return;
      }

      if (!signUpFields.value[0].value || !signUpFields.value[1].value) {
        toast.error("Please fill in all fields");
        return;
      }

      const response = await fetch('http://localhost/snapsell/auth.php?action=signup', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          name: signUpFields.value[0].value,
          email: signUpFields.value[1].value,
          password: signUpFields.value[2].value
        })
      });

      const data = await response.json();
      
      if (data.success) {
        toast.success("Registration successful! Please login.");
        isSignUpMode.value = false;
        // Clear the form
        signUpFields.value.forEach(field => field.value = '');
      } else {
        toast.error(data.error || "Registration failed");
      }
    } catch (error) {
      console.error('Signup error:', error);
      toast.error("Network error occurred");
    }
  };


  const handleVerifyOtp = async () => {
    try {
      if (!verificationCode.value) {
        toast.error('Please enter verification code');
        return;
      }

      const response = await fetch('http://localhost/snapsell/auth.php?action=verify-otp', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email: isPasswordReset.value ? forgotPasswordEmail.value : signInFields.value[0].value,
          code: verificationCode.value,
        }),
      });
      
      const data = await response.json();
      
      if (data.success) {
        if (isPasswordReset.value) {
          toast.success('Code verified! Please enter your new password.');
          showVerificationInput.value = false;
          showResetPasswordForm.value = true;
        } else {
          toast.success('Email verified successfully!');
          showVerificationInput.value = false;
          if (data.token) {
            localStorage.setItem('token', data.token);
            navigateTo('/dashboard');
          }
        }
      } else {
        toast.error(data.error || 'Invalid verification code');
      }
    } catch (error) {
      console.error('Verification error:', error);
      toast.error('Network error occurred');
    }
  };

  const handleLogin = async () => {
    try {
      const response = await fetch('http://localhost/snapsell/auth.php?action=login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email: signInFields.value[0].value,
          password: signInFields.value[1].value
        })
      });
      const data = await response.json();
      
      if(data.requiresVerification) {
        showVerificationInput.value = true;
        isPasswordReset.value = false;
        toast.info(data.message || 'Please verify your email');
      } else if(data.success) {
        toast.success("Login successful!");
        localStorage.setItem('token', data.token);
        navigateTo('/dashboard');
      } else {
        toast.error(data.error || "Login failed");
      }
    } catch (error) {
      console.error('Login error:', error);
      toast.error("Network error occurred");
    }
  };

  const resendCode = async () => {
    try {
      const endpoint = isPasswordReset.value ? 'send-otp' : 'send-otp';
      const emailToUse = isPasswordReset.value ? forgotPasswordEmail.value : email.value;
      
      const response = await fetch(`http://localhost/snapsell/auth.php?action=${endpoint}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: emailToUse, isPasswordReset: isPasswordReset.value }),
      });
      
      const data = await response.json();
      
      if (data.success) {
        toast.success('Verification code resent successfully');
      } else {
        toast.error(data.error || 'Failed to resend code');
      }
    } catch (error) {
      console.error('Resend code error:', error);
      toast.error('Network error occurred');
    }
  };

  const handleForgotPassword = async () => {
    try {
      if (!forgotPasswordEmail.value) {
        toast.error('Please enter your email');
        return;
      }

      const response = await fetch('http://localhost/snapsell/auth.php?action=send-otp', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email: forgotPasswordEmail.value,
          isPasswordReset: true
        }),
      });
      
      const data = await response.json();
      
      if (data.success) {
        toast.success('Reset code sent to your email');
        showVerificationInput.value = true;
        showForgotPasswordForm.value = false;
        isPasswordReset.value = true;
      } else {
        toast.error(data.error || 'Failed to send reset code');
      }
    } catch (error) {
      console.error('Forgot password error:', error);
      toast.error('Network error occurred');
    }
  };

  const handleResetPassword = async () => {
    try {
      if (newPassword.value !== confirmPassword.value) {
        toast.error('Passwords do not match');
        return;
      }

      const response = await fetch('http://localhost/snapsell/auth.php?action=reset-password', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          email: forgotPasswordEmail.value,
          newPassword: newPassword.value
        }),
      });

      const data = await response.json();

      if (data.success) {
        toast.success('Password reset successful');
        showResetPasswordForm.value = false;
        showForgotPasswordForm.value = false;
        isPasswordReset.value = false;
        // Clear sensitive data
        newPassword.value = '';
        confirmPassword.value = '';
        forgotPasswordEmail.value = '';
      } else {
        toast.error(data.error || 'Failed to reset password');
      }
    } catch (error) {
      console.error('Reset password error:', error);
      toast.error('Network error occurred');
    }
  };

  const showForgotPassword = () => {
    showForgotPasswordForm.value = true;
    showVerificationInput.value = false;
    isPasswordReset.value = false;
    showResetPasswordForm.value = false;
    // Reset all fields
    forgotPasswordEmail.value = '';
    verificationCode.value = '';
    newPassword.value = '';
    confirmPassword.value = '';
  };

  onMounted(() => {
    // If user is already logged in, redirect to dashboard
    const token = localStorage.getItem('token');
    if (token) {
      navigateTo('/dashboard');
    }
  });
  </script>
  
  <style scoped>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap");

*,
*::before,
*::after {
  padding: 0;
  margin: 0;
  box-sizing: border-box;
}

body,
input {
  font-family: "Poppins", sans-serif;
}

main {
  width: 100%;
  min-height: 100vh;
  overflow: hidden;
  background-color: #8371fd;
  padding: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.box {
  position: relative;
  width: 100%;
  max-width: 1020px;
  height: 640px;
  background-color: #fff;
  border-radius: 3.3rem;
  box-shadow: 0 60px 40px -30px rgba(0, 0, 0, 0.27);
}

.password-requirements {
    font-size: 0.65rem;
    margin-top: 0.5rem;
    margin-bottom: 1rem;
    padding: 0.5rem;
    background-color: white;
    border-radius: 0.5rem;
    width: 100%;
  }

.inner-box {
  position: absolute;
  width: calc(100% - 4.1rem);
  height: calc(100% - 4.1rem);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.forms-wrap {
  position: absolute;
  height: 100%;
  width: 45%;
  top: 0;
  left: 0;
  display: grid;
  grid-template-columns: 1fr;
  grid-template-rows: 1fr;
  transition: 0.8s ease-in-out;
}

form {
  max-width: 260px;
  width: 100%;
  margin: 0 auto;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-evenly;
  grid-column: 1 / 2;
  grid-row: 1 / 2;
  transition: opacity 0.02s 0.4s;
}

form.sign-up-form {
  opacity: 0;
  pointer-events: none;
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 2rem;
}

.logo {
  display: flex;
  align-items: center;
}

.logo img {
  width: 50px;
  margin-right: 0.3rem;
}

.logo h4 {
  font-size: 0.9rem;
  margin-top: -9px;
  letter-spacing: -0.5px;
  color: #151111;
}

.heading h2 {
  font-size: 2.1rem;
  font-weight: 600;
  color: #151111;
}

.heading h6 {
  color: #bababa;
  font-weight: 400;
  font-size: 0.75rem;
  display: inline;
}

.toggle {
  color: #151111;
  text-decoration: none;
  font-size: 0.75rem;
  font-weight: 500;
  transition: 0.3s;
}

.toggle:hover {
  color: #6c5ce7;
}

.input-wrap {
  position: relative;
  min-height: 37px;
  margin-bottom: 1.5rem;
}

.input-field {
  position: absolute;
  width: 100%;
  height: 100%;
  background: none;
  border: none;
  outline: none;
  border-bottom: 1px solid #bbb;
  padding: 0;
  font-size: 0.95rem;
  color: #151111;
  transition: 0.4s;
}

label {
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  font-size: 0.95rem;
  color: #bbb;
  pointer-events: none;
  transition: 0.4s;
}

label.active {
  font-size: 0.75rem;
  top: -2px;
  transform: translateY(0);
}

.input-field.active {
  border-bottom-color: #8371fd;
}

.sign-btn {
  width: 100%;
  padding: 12px;
  background: #4481eb;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.3s;
  margin: 1rem 0;
  position: relative;
  z-index: 1;
}

.sign-btn:hover:not(.disabled) {
  background: #2d6ad9;
}

.sign-btn.disabled {
  background: #ccc;
  cursor: not-allowed;
}

.text {
  color: #bbb;
  font-size: 0.7rem;
}

.text a {
  color: #bbb;
  transition: 0.3s;
}

.text a:hover {
  color: #8371fd;
}

main.sign-up-mode form.sign-in-form {
  opacity: 0;
  pointer-events: none;
}

main.sign-up-mode form.sign-up-form {
  opacity: 1;
  pointer-events: all;
}

main.sign-up-mode .forms-wrap {
  left: 55%;
}

main.sign-up-mode .carousel {
  left: 0%;
}

.carousel {
  position: absolute;
  height: 100%;
  width: 55%;
  left: 45%;
  top: 0;
  background-color: #f1eeff;
  border-radius: 2rem;
  display: grid;
  grid-template-rows: auto 1fr;
  padding-bottom: 2rem;
  overflow: hidden;
  transition: 0.8s ease-in-out;
}

.images-wrapper {
  display: grid;
  grid-template-columns: 1fr;
  grid-template-rows: 1fr;
  padding: 2rem;
  height: 70%;
}

.image {
  width: 100%;
  height: 100%;
  grid-column: 1/2;
  grid-row: 1/2;
  opacity: 0;
  transition: opacity 0.3s, transform 0.5s;
  object-fit: contain;
  max-height: 300px;
}

.img-1 {
  transform: translate(0, -50px);
}

.img-2 {
  transform: scale(0.4, 0.5);
}

.img-3 {
  transform: scale(0.3) rotate(-20deg);
}

.image.show {
  opacity: 1;
  transform: none;
}

.text-slider {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.text-wrap {
  max-height: 2.2rem;
  overflow: hidden;
  margin-bottom: 2.5rem;
}

.text-group {
  display: flex;
  flex-direction: column;
  text-align: center;
  transform: translateY(0);
  transition: 0.5s;
}

.text-group h2 {
  line-height: 2.2rem;
  font-weight: 600;
  font-size: 1.6rem;
}

.bullets {
  display: flex;
  align-items: center;
  justify-content: center;
}

.bullets span {
  display: block;
  width: 0.5rem;
  height: 0.5rem;
  background-color: #aaa;
  margin: 0 0.25rem;
  border-radius: 50%;
  cursor: pointer;
  transition: 0.3s;
}

.bullets span.active {
  width: 1.1rem;
  background-color: #8371fd;
  border-radius: 1rem;
}

@media (max-width: 850px) {
  .box {
    height: auto;
    max-width: 550px;
    overflow: hidden;
  }

  .inner-box {
    position: static;
    transform: none;
    width: revert;
    height: revert;
    padding: 2rem;
  }

  .forms-wrap {
    position: revert;
    width: 100%;
    height: auto;
  }

  form {
    max-width: revert;
    padding: 1.5rem 2.5rem 2rem;
    transition: transform 0.8s ease-in-out, opacity 0.45s linear;
  }

  .heading {
    margin: 2rem 0;
  }

  form.sign-up-form {
    transform: translateX(100%);
  }

  main.sign-up-mode form.sign-in-form {
    transform: translateX(-100%);
  }

  main.sign-up-mode form.sign-up-form {
    transform: translateX(0%);
  }

  .carousel {
    display: none;
  }

  .images-wrapper {
    display: none;
  }

  .text-slider {
    width: 100%;
  }

  .input-wrap {
    margin-bottom: 4rem;
  }

  .sign-btn {
    margin-bottom: 3rem;
  }
}

@media (max-width: 530px) {
  main {
    padding: 1rem;
  }

  .box {
    border-radius: 2rem;
  }

  .inner-box {
    padding: 1rem;
  }

  .carousel {
    display: none;
  }

  .text-wrap {
    margin-bottom: 1rem;
  }

  .text-group h2 {
    font-size: 1.2rem;
  }

  form {
    padding: 1rem 2rem 1.5rem;
  }

  .sign-btn {
    margin-bottom: 6rem;
  }
}

.password-requirements {
  margin: 0.5rem 0 1.5rem 0;
  font-size: 0.85rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 4px;
}

.password-requirements p {
  margin: 0.5rem 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.password-requirements p.valid {
  color: #28a745;
}

.password-requirements p i {
  font-size: 0.8rem;
}

.verification-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.verification-container {
  background-color: #fff;
  padding: 2rem;
  border-radius: 3.3rem;
  box-shadow: 0 60px 40px -30px rgba(0, 0, 0, 0.27);
  width: 100%;
  max-width: 400px;
}

.verification-container .logo {
  margin-bottom: 1rem;
}

.verification-container .heading {
  text-align: center;
  margin-bottom: 2rem;
}

.verification-container .heading h2 {
  margin-bottom: 0.5rem;
}

.verification-container .heading h6 {
  color: #bababa;
  font-size: 0.8rem;
  font-weight: 400;
}

.verification-container .input-wrap {
  margin-bottom: 2rem;
}

.password-requirements {
  margin: 1rem 0;
  font-size: 0.75rem;
  padding-left: 1rem;
}

.password-requirements p, .password-match-message {
  margin: 0.5rem 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.password-match-message {
  font-size: 0.75rem;
  margin: 0.5rem 0 1rem 1rem;
}

i {
  font-size: 0.8rem;
}

/* Form Container */
.sign-in-form {
  max-width: 400px;
  width: 100%;
  padding: 2rem;
}

/* Logo Section */
.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 2rem;
}

.logo img {
  width: 40px;
  margin-right: 0.5rem;
}

.logo h4 {
  font-size: 1.5rem;
  margin: 0;
}

/* Heading Section */
.heading {
  text-align: center;
  margin-bottom: 2rem;
}

.heading h2 {
  font-size: 1.75rem;
  margin-bottom: 0.5rem;
}

.heading h6 {
  font-size: 0.9rem;
  font-weight: normal;
  margin: 0;
}

/* Form Fields */
.actual-form {
  display: flex;
  flex-direction: column;
  height: auto;
  min-height: 300px;
  justify-content: space-between;
}

.input-wrap {
  position: relative;
  margin-bottom: 1rem;
}

.input-field {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.input-field:focus {
  border-color: #4481eb;
  outline: none;
}

label {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 1rem;
  color: #999;
  pointer-events: none;
  transition: 0.3s;
}

.input-field:focus ~ label,
label.active {
  top: -10px;
  left: 10px;
  font-size: 0.8rem;
  background: white;
  padding: 0 5px;
  color: #4481eb;
}

/* Password Requirements */
.password-requirements {
  background: #f8f9fa;
  padding: 0.75rem;
  border-radius: 4px;
  margin-top: 1rem;
  width: 100%;
  font-size: 0.65rem;  /* Smaller font size */
}

.password-requirements p {
  margin: 0.3rem 0;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  color: #666;
}

.password-requirements p i {
  font-size: 0.65rem;  /* Match the text size */
}

.password-requirements p.valid {
  color: #28a745;
}

/* Buttons */
.sign-btn {
  width: 100%;
  padding: 12px;
  background: #4481eb;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.3s;
}

.sign-btn:hover {
  background: #2d6ad9;
}

.sign-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* Helper Text */
.text {
  text-align: center;
  margin-top: 1rem;
  font-size: 0.9rem;
  color: #666;
}

.text a {
  color: #4481eb;
  text-decoration: none;
}

.text a:hover {
  text-decoration: underline;
}

/* Verification Code Input */
input[type="text"][pattern="[0-9]*"] {
  letter-spacing: 0.5rem;
  text-align: center;
  font-size: 1.2rem;
}

/* Toggle Links */
.toggle {
  color: #4481eb;
  text-decoration: none;
  font-weight: 600;
}

.toggle:hover {
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
  .sign-in-form {
    padding: 1.5rem;
  }

  .heading h2 {
    font-size: 1.5rem;
  }
}

/* Update form styles */
.sign-up-form {
  opacity: 0;
  pointer-events: none;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
  padding: 2rem 0;  /* Revert to original padding */
  max-width: 320px;  /* Slightly wider form */
}

.inputs-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1rem;
}

/* Update just the password requirements styles */
.password-requirements-container {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

.password-requirements {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
  justify-content: center;
  background: #f8f9fa;
  padding: 0.4rem;
  border-radius: 4px;
  font-size: 0.6rem;  /* Reduced font size */
}

.password-requirements p {
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: #666;
  padding: 0.15rem 0.3rem;  /* Reduced padding */
  white-space: nowrap;  /* Prevent text wrapping */
}

.password-requirements p i {
  font-size: 0.6rem;  /* Reduced icon size */
  width: auto;
  margin-right: 0.1rem;
}

.password-requirements p.valid {
  color: #28a745;
}

/* Update actual form layout */
.actual-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;  /* Revert to original spacing */
}

/* Ensure sign button has proper spacing */
.sign-btn {
  margin: 1rem 0;
}
  </style>
  