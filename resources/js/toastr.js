import toastr from 'toastr';	// Импортировать нужный пакет
import 'toastr/build/toastr.min.css';	// Также для работы нужен CSS-файл

export function useToastr() {
	toastr.options.positionClass = 'toast-bottom-right';	// Указать - чтобы уведомление появлялось в правом нижнем углу: Иначе будет справа наверху
	toastr.options.closeButton = true;	// Кнопка закрытия
	toastr.options.progressBar = true;	// анимация оставшегося времени
	return toastr;
}