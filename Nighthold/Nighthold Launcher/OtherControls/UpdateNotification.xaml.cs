using MagicStorm_Launcher.Nighthold;
using System.Diagnostics;
using System.Windows;
using System.Windows.Controls;

namespace MagicStorm_Launcher.OtherControls
{
    /// <summary>
    /// Interaction logic for UpdateNotification.xaml
    /// </summary>
    public partial class UpdateNotification : UserControl
    {
        public UpdateNotification()
        {
            InitializeComponent();
        }

        private void BtnUpdate_Click(object sender, RoutedEventArgs e)
        {
            Process.Start("Nighthold Updater.exe");

            AppHandler.Shutdown();
        }

        private void BtnCancel_Click(object sender, RoutedEventArgs e)
        {
            AppHandler.Shutdown();
        }
    }
}
