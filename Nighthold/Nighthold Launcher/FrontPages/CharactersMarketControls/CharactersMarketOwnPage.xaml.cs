using MagicStorm_Launcher.FrontPages.CharactersMarketControls.Childs;
using MagicStorm_Launcher.FrontPages.CharactersMarketControls.Windows;
using MagicStorm_Launcher.Nighthold;
using System;
using System.Diagnostics;
using System.Windows;
using System.Windows.Controls;
using WebHandler;

namespace MagicStorm_Launcher.FrontPages.CharactersMarketControls
{
    /// <summary>
    /// Interaction logic for CharactersMarketOwnPage.xaml
    /// </summary>
    public partial class CharactersMarketOwnPage : UserControl
    {
        public CharactersMarketOwnPage()
        {
            InitializeComponent();
        }

        private void BtnReturnHome_Click(object sender, RoutedEventArgs e)
        {
            SPMarketRows.Children.Clear();
            Visibility = Visibility.Hidden;
            AnimHandler.FadeIn(SystemTray.magicstormLauncher.marketPage, 300);
        }

        public async void LoadMarketOwnPage()
        {
            SystemTray.magicstormLauncher.mainPage.Visibility = Visibility.Hidden;
            AnimHandler.FadeIn(this, 300);

            try
            {
                var marketList = CharactersMarketClass.CharactersMarketOwnList.FromJson(await CharactersMarketClass.GetCharactersMarketOwnListJson(MagicStormLauncher.LoginUsername, MagicStormLauncher.LoginPassword));

                SPMarketRows.Children.Clear();

                if (marketList != null)
                {
                    foreach (var marketItem in marketList)
                    {
                        var marketRow = new MarketRow2(marketItem.MarketId, marketItem.Guid, marketItem.Name, marketItem.Class, marketItem.Race, marketItem.Gender, marketItem.Level, marketItem.PriceDp, marketItem.RealmId, marketItem.RealmName);
                        SPMarketRows.Children.Add(marketRow);
                        AnimHandler.MoveUpAndFadeIn300Ms(marketRow);
                    }

                    //SimulateRealmSelection();
                }
            }
            catch (Exception ex)
            {
                ExceptionHandler.AskToReport(ex, new StackTrace(true).GetFrame(0).GetFileName(), new StackTrace(ex, true).GetFrame(0).GetFileLineNumber());
            }
        }

        private void SellCharacterBtn_Click(object sender, RoutedEventArgs e)
        {
            SellPopup sellPopup = new SellPopup();
            sellPopup.Owner = SystemTray.magicstormLauncher;
            sellPopup.ShowDialog();
        }
    }
}
