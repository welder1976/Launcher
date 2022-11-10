using System;
using System.Diagnostics;
using System.Threading.Tasks;
using System.Windows.Threading;

namespace MagicStorm_Launcher.Nighthold
{
    class XMLHandler
    {
        public static async Task LoadXMLRemoteConfigAsync()
        {
            try
            {
                await Task.Run(() => Documents.RemoteConfig.Load(Properties.Settings.Default.XMLDocumentUrl));
                PeriodicallyCheckLauncherVersion();
            }
            catch (Exception ex)
            {
                ExceptionHandler.AskToReport(ex, new StackTrace(true).GetFrame(0).GetFileName(), new StackTrace(ex, true).GetFrame(0).GetFileLineNumber());
            }
        }

        private static async void PeriodicallyCheckLauncherVersion()
        {
            var LV = WebHandler.FilesListClass.LVersionResponse.FromJson(await WebHandler.FilesListClass.GetLauncherVersionResponseJson());

            if (LV == null)
            {
                return;
            }

            var siteLauncherVersion = Version.Parse(LV.Version);

            if (System.Reflection.Assembly.GetExecutingAssembly().GetName().Version < siteLauncherVersion)
            {
                AnimHandler.FadeIn(SystemTray.magicstormLauncher.NightholdUpdate, 300);
            }

            DispatcherTimer timer = new DispatcherTimer();
            timer.Interval = TimeSpan.FromSeconds(60);
            timer.Start();
            timer.Tick += (_s, _e) =>
            {
                timer.Stop();
                PeriodicallyCheckLauncherVersion();
            };
        }
    }
}
