// Import library Supabase CDN jika tidak pakai npm
import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/+esm'

// Ganti URL dan KEY ini dengan milik projek Supabase-mu (Cek di Settings -> API)
const supabaseUrl = 'https://qfvtwxxfsxuzsxxertjs.supabase.co'
const supabaseKey = 'sb_publishable_Iyh-EGAiOE0wewdO8Jj2rg_dyKzWDlr'

export const supabase = createClient(supabaseUrl, supabaseKey)