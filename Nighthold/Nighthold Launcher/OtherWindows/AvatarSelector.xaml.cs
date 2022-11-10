using MagicStorm_Launcher.Nighthold;
using MagicStorm_Launcher.OtherWindows.Childs;
using System;
using System.Collections;
using System.Diagnostics;
using System.Globalization;
using System.Reflection;
using System.Resources;
using System.Threading.Tasks;
using System.Windows;
using WebHandler;

namespace MagicStorm_Launcher.OtherWindows
{
    /// <summary>
    /// Interaction logic for AvatarSelector.xaml
    /// </summary>
    public partial class AvatarSelector : Window
    {
        public string pAvatarPath;
        public UriKind pAvatarUriKind;

        public AvatarSelector()
        {
            InitializeComponent();
        }

        private void BtnYes_Click(object sender, RoutedEventArgs e)
        {
            DialogResult = true;
        }

        private async void Window_Loaded(object sender, RoutedEventArgs e)
        {
            AnimHandler.FadeIn(SystemTray.magicstormLauncher.OverlayBlur, 300);

            // Load DB avatars
            // await LoadAvatar();


            // Load Race Icons into the avatars list
            //List<string> resourceNames = new List<string>();
            try
            {
                var assembly = Assembly.GetExecutingAssembly();
                var rm = new ResourceManager(assembly.GetName().Name + ".g", assembly);

                var list = rm.GetResourceSet(CultureInfo.CurrentCulture, true, true);
                foreach (DictionaryEntry item in list)
                {
                    //resourceNames.Add((string)item.Key);
                    string resName = (string)item.Key;
                    if (resName.StartsWith("assets/race%20icons/"))
                    {
                        WPAvatars.Children.Add(new AvatarSelectionSpawn(this, resName.Replace("%20", " ")));
                    }
                }

                rm.ReleaseAllResources();
            }
            catch (Exception ex)
            {
                ExceptionHandler.AskToReport(ex, new StackTrace(true).GetFrame(0).GetFileName(), new StackTrace(ex, true).GetFrame(0).GetFileLineNumber());
            }
        }

        private async Task LoadAvatar()
        {
            try
            {
                var dbAvatarsCollection = AuthClass.DBAvatarsList.FromJson(await AuthClass.GetDBAvatarsListJson(MagicStormLauncher.LoginUsername, MagicStormLauncher.LoginPassword));

                WPAvatars.Children.Clear();

                if (dbAvatarsCollection != null)
                {
                    foreach (var avatar in dbAvatarsCollection)
                    {
                        WPAvatars.Children.Add(new AvatarSelectionSpawn(this, avatar.Url.Replace("%20", " "), true));
                    }
                }
            }
            catch (Exception ex)
            {
                ExceptionHandler.AskToReport(ex, new StackTrace(true).GetFrame(0).GetFileName(), new StackTrace(ex, true).GetFrame(0).GetFileLineNumber());
            }
        }

        private void Window_Closing(object sender, System.ComponentModel.CancelEventArgs e)
        {
            AnimHandler.FadeOut(SystemTray.magicstormLauncher.OverlayBlur, 300);
        }
    }
}
