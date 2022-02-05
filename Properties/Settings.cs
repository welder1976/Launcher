// Decompiled with JetBrains decompiler
// Type: Launcher.Properties.Settings
// Assembly: AsgaronLauncher, Version=1.0.0.5, Culture=neutral, PublicKeyToken=null
// MVID: 5B93C4E0-CE7C-4424-A533-57788683C619
// Assembly location: D:\torrent\World of Warcraft\AsgaronLauncher.exe

using System.CodeDom.Compiler;
using System.Configuration;
using System.Diagnostics;
using System.Runtime.CompilerServices;

namespace Launcher.Properties
{
  [CompilerGenerated]
  [GeneratedCode("Microsoft.VisualStudio.Editors.SettingsDesigner.SettingsSingleFileGenerator", "15.0.1.0")]
  internal sealed class Settings : ApplicationSettingsBase
  {
    private static Settings defaultInstance = (Settings) SettingsBase.Synchronized((SettingsBase) new Settings());

    public static Settings Default
    {
      get
      {
        return Settings.defaultInstance;
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100/launcher/?type=patchlist")]
    public string PatchDownloadURL
    {
      get
      {
        return (string) this[nameof (PatchDownloadURL)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100/launcher/?type=patchtodel")]
    public string PatchToDelete
    {
      get
      {
        return (string) this[nameof (PatchToDelete)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100/launcher/?type=l-version")]
    public string launcherVersionUrl
    {
      get
      {
        return (string) this[nameof (launcherVersionUrl)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100/launcher/?type=news")]
    public string launcherNewsFileUrl
    {
      get
      {
        return (string) this[nameof (launcherNewsFileUrl)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100/launcher/?type=l-updates")]
    public string launcherUpdates
    {
      get
      {
        return (string) this[nameof (launcherUpdates)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100/launcher/?type=realmlist")]
    public string realmlistURL
    {
      get
      {
        return (string) this[nameof (realmlistURL)];
      }
    }

    [UserScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("")]
    public string username
    {
      get
      {
        return (string) this[nameof (username)];
      }
      set
      {
        this[nameof (username)] = (object) value;
      }
    }

    [UserScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("")]
    public string password
    {
      get
      {
        return (string) this[nameof (password)];
      }
      set
      {
        this[nameof (password)] = (object) value;
      }
    }

    [UserScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("False")]
    public bool autologin
    {
      get
      {
        return (bool) this[nameof (autologin)];
      }
      set
      {
        this[nameof (autologin)] = (object) value;
      }
    }

    [UserScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("Не задано")]
    public string gameFolder
    {
      get
      {
        return (string) this[nameof (gameFolder)];
      }
      set
      {
        this[nameof (gameFolder)] = (object) value;
      }
    }

    [UserScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("0")]
    public int progressBarType
    {
      get
      {
        return (int) this[nameof (progressBarType)];
      }
      set
      {
        this[nameof (progressBarType)] = (object) value;
      }
    }

    [UserScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("0")]
    public long downloadSpeedLimit
    {
      get
      {
        return (long) this[nameof (downloadSpeedLimit)];
      }
      set
      {
        this[nameof (downloadSpeedLimit)] = (object) value;
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100")]
    public string HomeSite
    {
      get
      {
        return (string) this[nameof (HomeSite)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("https://vk.com/public202742511")]
    public string HomeSiteForum
    {
      get
      {
        return (string) this[nameof (HomeSiteForum)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100/status/")]
    public string ServerStatus
    {
      get
      {
        return (string) this[nameof (ServerStatus)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100/status/online.php")]
    public string ServerGamersCount
    {
      get
      {
        return (string) this[nameof (ServerGamersCount)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100")]
    public string HomeSiteReg
    {
      get
      {
        return (string) this[nameof (HomeSiteReg)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("https://vk.com/public202742511")]
    public string HomeSiteVK
    {
      get
      {
        return (string) this[nameof (HomeSiteVK)];
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100/launcher")]
    public string GetLauncher
    {
      get
      {
        return (string) this[nameof (GetLauncher)];
      }
    }

    [UserScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("http://185.221.154.100/launcher/?type=goodruru")]
    public string GoodFilesURL
    {
      get
      {
        return (string) this[nameof (GoodFilesURL)];
      }
      set
      {
        this[nameof (GoodFilesURL)] = (object) value;
      }
    }

    [ApplicationScopedSetting]
    [DebuggerNonUserCode]
    [DefaultSettingValue("WOWMagic.exe")]
    public string clientExe
    {
      get
      {
        return (string) this[nameof (clientExe)];
      }
    }
  }
}
