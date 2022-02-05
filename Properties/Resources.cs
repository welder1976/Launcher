// Decompiled with JetBrains decompiler
// Type: Launcher.Properties.Resources
// Assembly: AsgaronLauncher, Version=1.0.0.5, Culture=neutral, PublicKeyToken=null
// MVID: 5B93C4E0-CE7C-4424-A533-57788683C619
// Assembly location: D:\torrent\World of Warcraft\AsgaronLauncher.exe

using System.CodeDom.Compiler;
using System.ComponentModel;
using System.Diagnostics;
using System.Globalization;
using System.Resources;
using System.Runtime.CompilerServices;

namespace Launcher.Properties
{
  [GeneratedCode("System.Resources.Tools.StronglyTypedResourceBuilder", "4.0.0.0")]
  [DebuggerNonUserCode]
  [CompilerGenerated]
  internal class Resources
  {
    private static ResourceManager resourceMan;
    private static CultureInfo resourceCulture;

    internal Resources()
    {
    }

    [EditorBrowsable(EditorBrowsableState.Advanced)]
    internal static ResourceManager ResourceManager
    {
      get
      {
        if (Launcher.Properties.Resources.resourceMan == null)
          Launcher.Properties.Resources.resourceMan = new ResourceManager("Launcher.Properties.Resources", typeof (Launcher.Properties.Resources).Assembly);
        return Launcher.Properties.Resources.resourceMan;
      }
    }

    [EditorBrowsable(EditorBrowsableState.Advanced)]
    internal static CultureInfo Culture
    {
      get
      {
        return Launcher.Properties.Resources.resourceCulture;
      }
      set
      {
        Launcher.Properties.Resources.resourceCulture = value;
      }
    }
  }
}
