import { supabase } from './supabase.js'

const formRegister = document.getElementById('form-register')

if (formRegister) {
    window.addEventListener('pageshow', () => {
        formRegister.reset()

        document.getElementById('nama-lengkap').value = ''
        document.getElementById('email').value = ''
        document.getElementById('nomor-hp').value = ''
        document.getElementById('password').value = ''
        document.getElementById('konfirmasi-password').value = ''
        document.getElementById('terms').checked = false
    })
}

formRegister.addEventListener('submit', async (e) => {
    e.preventDefault()

    const nama = document.getElementById('nama-lengkap').value
    const email = document.getElementById('email').value
    const nomorHp = document.getElementById('nomor-hp').value
    const password = document.getElementById('password').value
    const konfirmasi = document.getElementById('konfirmasi-password').value

    // Validasi kecocokan kata sandi sebelum dikirim
    if (password !== konfirmasi) {
        alert("Kata sandi dan konfirmasi tidak cocok!")
        return
    }

    // Mengirim data ke Supabase Auth (Langkah A)
    const { data, error } = await supabase.auth.signUp({
        email: email,
        password: password,
        options: {
            data: {
                nama_lengkap: nama,
                nomor_hp: nomorHp
            }
        }
    })

    if (error) {
        alert("Registrasi Gagal: " + error.message)
    } else {
        alert("Registrasi Berhasil! Cek email kamu untuk konfirmasi akun.")
        try {
            localStorage.setItem('zakaai_full_name', nama)
        } catch (err) {
            console.warn('LocalStorage tidak tersedia:', err)
        }
        formRegister.reset()
        document.getElementById('nama-lengkap').value = ''
        document.getElementById('email').value = ''
        document.getElementById('nomor-hp').value = ''
        document.getElementById('password').value = ''
        document.getElementById('konfirmasi-password').value = ''
        document.getElementById('terms').checked = false
    }
})