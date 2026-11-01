export const experienceAccents: Record<string, string> = {
    blue: 'from-[#0a66c2] to-sky-400',
    indigo: 'from-indigo-600 to-violet-400',
    teal: 'from-teal-600 to-emerald-400',
    amber: 'from-amber-600 to-orange-400',
    rose: 'from-rose-600 to-pink-400',
    slate: 'from-slate-800 to-slate-500',
};

export const projectCovers: Record<string, string> = {
    blue: 'bg-[radial-gradient(circle_at_18%_12%,#93c5fd_0%,transparent_55%),radial-gradient(circle_at_88%_88%,#2563eb_0%,transparent_62%),linear-gradient(135deg,#0f172a_0%,#172554_55%,#1d4ed8_100%)]',
    amber: 'bg-[radial-gradient(circle_at_82%_18%,#fcd34d_0%,transparent_55%),radial-gradient(circle_at_8%_92%,#f97316_0%,transparent_58%),linear-gradient(135deg,#1c1917_0%,#431407_58%,#9a3412_100%)]',
    teal: 'bg-[radial-gradient(circle_at_14%_16%,#99f6e4_0%,transparent_55%),radial-gradient(circle_at_92%_86%,#0d9488_0%,transparent_60%),linear-gradient(135deg,#022c22_0%,#064e3b_55%,#0f766e_100%)]',
    indigo: 'bg-[radial-gradient(circle_at_20%_16%,#c7d2fe_0%,transparent_55%),radial-gradient(circle_at_88%_84%,#4f46e5_0%,transparent_60%),linear-gradient(135deg,#111827_0%,#1e1b4b_55%,#4338ca_100%)]',
    rose: 'bg-[radial-gradient(circle_at_18%_14%,#fecdd3_0%,transparent_55%),radial-gradient(circle_at_88%_86%,#e11d48_0%,transparent_60%),linear-gradient(135deg,#1f1115_0%,#4c0519_55%,#9f1239_100%)]',
    violet: 'bg-[radial-gradient(circle_at_18%_14%,#e9d5ff_0%,transparent_55%),radial-gradient(circle_at_88%_86%,#7c3aed_0%,transparent_60%),linear-gradient(135deg,#140f1f_0%,#2e1065_55%,#6d28d9_100%)]',
};

export function projectNumber(index: number): string {
    return String(index + 1).padStart(2, '0');
}
