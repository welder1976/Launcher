using System;
using System.Reflection;
using System.Windows;
using WebHandler;

namespace MagicStorm_Launcher.Nighthold
{
    class ExceptionHandler
    {
        public static async void AskToReport(Exception ex, string fileName, int lineNumber)
        {
            MessageBoxResult mBoxResult = MessageBox.Show(ex.Message + "\r\n \r\n" + "Launcher file: " + fileName + "\r\n \r\n" + "Line number: " + lineNumber,
                "Сообщить об этой ошибке разработчикам?", MessageBoxButton.YesNo, MessageBoxImage.Question);
            if (mBoxResult == MessageBoxResult.Yes)
            {
                await DiscordClass.SendNewIssueReport(MagicStormLauncher.LoginUsername,
                    Assembly.GetExecutingAssembly().GetName().Version.ToString(),
                    $"\"{fileName}\" at line ({lineNumber})",
                    ex.Message);
            }
        }

        public static async void AskToReport(string customError)
        {
            MessageBoxResult mBoxResult = MessageBox.Show(customError, "Сообщить об этой ошибке разработчикам?", MessageBoxButton.YesNo, MessageBoxImage.Question);

            if (mBoxResult == MessageBoxResult.Yes)
            {
                await DiscordClass.SendNewIssueReport(MagicStormLauncher.LoginUsername, Assembly.GetExecutingAssembly().GetName().Version.ToString(), customError, "");
            }
        }
    }
}
