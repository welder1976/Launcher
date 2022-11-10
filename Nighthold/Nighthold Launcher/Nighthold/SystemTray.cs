using System.Windows.Forms;

namespace MagicStorm_Launcher.Nighthold
{
    class SystemTray
    {
        public static NotifyIcon notifier = new NotifyIcon();

        public static MagicStormLauncher magicstormLauncher;

        public SystemTray(MagicStormLauncher _nightholdLauncher)
        {
            magicstormLauncher = _nightholdLauncher;
        }
    }
}
