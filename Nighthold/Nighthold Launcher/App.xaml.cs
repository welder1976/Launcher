using System.Windows;

namespace MagicStorm_Launcher
{
    /// <summary>
    /// Interaction logic for App.xaml
    /// </summary>
    public partial class App : Application
    {
        private void Application_Startup(object sender, StartupEventArgs e)
        {
            MagicStormLauncher WindowParent = new MagicStormLauncher();
            WindowParent.SetArguments(e.Args);
        }
    }
}
