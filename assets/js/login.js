import { supabase } from './supabase.js'

const formLogin = document.getElementById('form-login')
const btnGoogle = document.getElementById('btn-google')
const btnApple = document.getElementById('btn-apple')

if (formLogin) {
    window.addEventListener('pageshow', () => {
        formLogin.reset();

        document.getElementById('email').value = '';
        document.getElementById('password').value = '';
    });
}

// 1. Logika Login Email Biasa
formLogin.addEventListener('submit', async (e) => {
    e.preventDefault()
    const email = document.getElementById('email').value
    const password = document.getElementById('password').value

    const { data, error } = await supabase.auth.signInWithPassword({
        email: email,
        password: password
    })

    if (error) {
        alert("Login Gagal: " + error.message)
    } else {
        alert("Selamat Datang Kembali!")
        formLogin.reset()
        window.location.href = 'index.php'
    }
})

// 2. Logika Login Google OAuth
btnGoogle.addEventListener('click', async (e) => {
    e.preventDefault() // Mencegah form melakukan reload
    const { data, error } = await supabase.auth.signInWithOAuth({
        provider: 'google',
        options: {
            redirectTo: window.location.origin + '/index.php' // Otomatis balik ke index.php setelah login sukses
        }
    })
    if (error) alert("Gagal Login Google: " + error.message)
})

// 3. Logika Login Apple OAuth
btnApple.addEventListener('click', async (e) => {
    e.preventDefault() // Mencegah form melakukan reload
    const { data, error } = await supabase.auth.signInWithOAuth({
        provider: 'apple',
        options: {
            redirectTo: window.location.origin + '/index.php'
        }
    })
    if (error) alert("Gagal Login Apple: " + error.message)
})