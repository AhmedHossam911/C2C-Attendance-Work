<footer
    class="relative lg:fixed lg:bottom-0 lg:right-0 z-20 bg-white/90 dark:bg-[#0f172a]/90 backdrop-blur-md border-t border-slate-200 dark:border-slate-800/60 py-4 transition-all duration-300 ease-in-out"
    :class="sidebarCollapsed ? 'left-0 lg:left-20' : 'left-0 lg:left-72'">
    <div class="w-full max-w-[1600px] mx-auto px-4 md:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Left: Developer Info -->
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <span>Developed by</span>
                <a href="https://linktr.ee/Ahmed_911" target="_blank"
                    class="font-bold text-c2c-blue-600 dark:text-blue-400 hover:text-c2c-teal-600 dark:hover:text-teal-400 flex items-center gap-1 transition-colors">
                    Ahmed Hossam C2C president
                </a>
            </div>

            <!-- Right: Socials -->
            <div class="flex gap-6 opacity-80 hover:opacity-100 transition-opacity">
                <a href="https://www.facebook.com/C2C.BIS.Helwan" target="_blank"
                    class="text-slate-400 hover:text-c2c-blue-600 hover:scale-110 transition-all"><i
                        class="bi bi-facebook text-lg"></i></a>
                <a href="https://www.tiktok.com/@c2c.bis.helwan" target="_blank"
                    class="text-slate-400 hover:text-pink-500 hover:scale-110 transition-all"><i
                        class="bi bi-tiktok text-lg"></i></a>
                <a href="https://www.instagram.com/c2c.bis.helwan/" target="_blank"
                    class="text-slate-400 hover:text-purple-500 hover:scale-110 transition-all"><i
                        class="bi bi-instagram text-lg"></i></a>
                <a href="https://www.linkedin.com/company/c2c-bis-helwan/" target="_blank"
                    class="text-slate-400 hover:text-blue-600 hover:scale-110 transition-all"><i
                        class="bi bi-linkedin text-lg"></i></a>
            </div>
        </div>
    </div>
</footer>
