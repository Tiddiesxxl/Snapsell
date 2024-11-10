<template>
    <main :class="{ 'sign-up-mode': isSignUpMode }">
      <div class="box">
        <div class="inner-box">
          <div class="forms-wrap">
            <!-- Sign-In Form -->
            <form @submit.prevent="handleLogin" autocomplete="off" class="sign-in-form">
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
                <div class="input-wrap" v-for="(field, index) in signInFields" :key="index">
                  <input
                    :type="field.type"
                    minlength="4"
                    class="input-field"
                    :class="{ active: field.active }"
                    v-model="field.value"
                    @focus="setActive(index, 'signIn')"
                    @blur="removeActive(index, 'signIn')"
                    required
                  />
                  <label :class="{ active: field.active || field.value }">{{ field.label }}</label>
                </div>
                <input type="submit" value="Sign In" class="sign-btn" />
                <p class="text">
                  Forgotten your password or login details?
                  <a href="#">Get help</a> signing in
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
                <div class="input-wrap" v-for="(field, index) in signUpFields" :key="index">
                  <input
                    :type="field.type"
                    minlength="4"
                    class="input-field"
                    :class="{ active: field.active }"
                    v-model="field.value"
                    @focus="setActive(index, 'signUp')"
                    @blur="removeActive(index, 'signUp')"
                    required
                  />
                  <label :class="{ active: field.active || field.value }">{{ field.label }}</label>
                  
                  <!-- Add password requirements feedback -->
                  <div v-if="field.type === 'password'" class="password-requirements">
                    <p :class="{ valid: hasMinLength, invalid: !hasMinLength }">
                      ✓ At least 8 characters
                    </p>
                    <p :class="{ valid: hasUpperCase, invalid: !hasUpperCase }">
                      ✓ At least one uppercase letter
                    </p>
                    <p :class="{ valid: hasLowerCase, invalid: !hasLowerCase }">
                      ✓ At least one lowercase letter
                    </p>
                    <p :class="{ valid: hasNumber, invalid: !hasNumber }">
                      ✓ At least one number
                    </p>
                    <p :class="{ valid: hasSpecialChar, invalid: !hasSpecialChar }">
                      ✓ At least one special character
                    </p>
                  </div>
                </div>
                <input 
                  type="submit" 
                  value="Sign Up" 
                  class="sign-btn" 
                  :disabled="!isPasswordValid"
                  :class="{ 'disabled': !isPasswordValid }"
                />
              </div>
            </form>

            <!-- Verification Modal -->
            <div v-if="showVerificationInput" class="verification-modal">
              <div class="verification-container">
                <div class="logo">
                  <img src="~assets/images/pc1.png" alt="easyclass" />
                  <h4>Snap Sell</h4>
                </div>
                <div class="heading">
                  <h2>Verify Account</h2>
                  <h6>Please enter the verification code sent to your email</h6>
                </div>
                <div class="actual-form">
                  <div class="input-wrap">
                    <input
                      type="text"
                      class="input-field"
                      :class="{ active: isVerificationActive }"
                      v-model="verificationCode"
                      @focus="setVerificationActive(true)"
                      @blur="setVerificationActive(false)"
                      required
                    />
                    <label :class="{ active: isVerificationActive || verificationCode }">
                      Verification Code
                    </label>
                  </div>
                  <input 
                    type="button" 
                    value="Verify" 
                    class="sign-btn" 
                    @click="verifyCode"
                  />
                </div>
              </div>
            </div>
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
  import { ref, watch, computed } from 'vue';
  import { useRoute } from 'vue-router';
  import { useToast } from 'vue-toastification'

  const isSignUpMode = ref(false);
  const activeImageIndex = ref(0);
  
  // Define form fields
  const signInFields = ref([
    { type: 'text', value: '', label: 'Name', active: false },
    { type: 'password', value: '', label: 'Password', active: false }
  ]);
  
  const signUpFields = ref([
    { type: 'text', value: '', label: 'Name', active: false },
    { type: 'email', value: '', label: 'Email', active: false },
    { type: 'password', value: '', label: 'Password', active: false }
  ]);
  
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
    if (!isPasswordValid.value) {
      toast.error("Please meet all password requirements");
      return;
    }
    
    try {
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
      if(data.success) {
        toast.success("Registration successful! Please login.");
        // Optional: switch to login form
        isSignUpMode.value = false;
      } else {
        toast.error(data.error || "Registration failed");
      }
    } catch (error) {
      toast.error("Network error occurred");
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
                name: signInFields.value[0].value,
                password: signInFields.value[1].value
            })
        });
        const data = await response.json();
        
        if(data.requiresVerification) {
            // Store user ID temporarily
            localStorage.setItem('temp_user_id', data.userId);
            showVerificationInput.value = true;
            toast.info(data.message);
        } else if(data.success) {
            toast.success("Login successful!");
            localStorage.setItem('token', data.token);
            navigateTo('/dashboard');
        } else {
            toast.error(data.error || "Login failed");
        }
    } catch (error) {
        toast.error("Network error occurred");
    }
  };

  const password = computed(() => signUpFields.value[2].value);

  const hasMinLength = computed(() => password.value.length >= 8);
  const hasUpperCase = computed(() => /[A-Z]/.test(password.value));
  const hasLowerCase = computed(() => /[a-z]/.test(password.value));
  const hasNumber = computed(() => /[0-9]/.test(password.value));
  const hasSpecialChar = computed(() => /[!@#$%^&*(),.?":{}|<>]/.test(password.value));

  const isPasswordValid = computed(() => 
    hasMinLength.value && 
    hasUpperCase.value && 
    hasLowerCase.value && 
    hasNumber.value && 
    hasSpecialChar.value
  );

  const showVerificationInput = ref(false);
  const verificationCode = ref('');
  const isVerificationActive = ref(false);

  const verifyCode = async () => {
    try {
        const userId = localStorage.getItem('temp_user_id');
        const response = await fetch('http://localhost/snapsell/auth.php?action=verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                userId: userId,
                code: verificationCode.value
            }),
            credentials:'include'
        });
        
        const data = await response.json();
        if (data.success) {
            localStorage.removeItem('temp_user_id');
            localStorage.setItem('token', data.token);
            toast.success('Verification successful!');
            showVerificationInput.value = false;
            navigateTo('/dashboard');
        } else {
            toast.error(data.error || 'Verification failed');
        }
    } catch (error) {
        toast.error('Network error occurred');
    }
  };

  const setVerificationActive = (value) => {
    isVerificationActive.value = value;
  };
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
  height: 37px;
  margin-bottom: 2rem;
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
  display: inline-block;
  width: 100%;
  height: 43px;
  background-color: #8371fd;
  color: #fff;
  border: none;
  cursor: pointer;
  border-radius: 0.8rem;
  font-size: 0.8rem;
  margin-bottom: 4rem;
  transition: 0.3s;
}

.sign-btn:hover {
  background-color: #6c5ce7;
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
  position: absolute;
  top: 370%;
  left: 0;
  font-size: 0.7rem;
  margin-top: 0.5rem;
  z-index: 1;
}

.password-requirements p {
  margin: 0.1rem 0;
  transition: color 0.3s ease;
}

.valid {
  color: #4CAF50;
}

.invalid {
  color: #f44336;
}

.sign-btn.disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.sign-btn.disabled:hover {
  background-color: #cccccc;
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

/* Remove the old verification form styles */
.verification-form {
  display: none;
}
  </style>
  