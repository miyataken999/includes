Runner.namespace('Runner.lang');

Runner.lang.constants = {

	current_language: "Japanese",
	
//	for list page
	TEXT_FIRST: '先頭',
	TEXT_PREVIOUS: '前',
	TEXT_NEXT: '次',
	TEXT_LAST: '最後',
	TEXT_PROCEED_TO: '進む',
	TEXT_DETAIL_NOT_SAVED: '%s にあるレコードは保存されていません',
	TEXT_NO_RECORDS: 'レコードがありませんでした。',
	TEXT_DETAIL_GOTO: '移動',
	TEXT_SHOW_ALL: '全て表示',
	TEXT_SHOW_OPTIONS: 'オプションの表示',
	TEXT_HIDE_OPTIONS: 'オプションの非表示',
	TEXT_SEARCH_SHOW_OPTIONS:'検索オプションを表示する',
	TEXT_SEARCH_HIDE_OPTIONS:'検索オプションを隠す',
	TEXT_SHOW_SEARCH_PANEL:'Show search panel',
	TEXT_HIDE_SEARCH_PANEL:'Hide search panel',


	TEXT_LOADING: 'ローディング',
	TEXT_DELETE_CONFIRM: '本当にこのレコードを削除しますか？',
	TEXT_PAGE: 'ページ',
	TEXT_PAGEMAX: '/',

//	for editing pages	
	TEXT_INVALID_CAPTCHA_CODE: '不正なセキュリティコードです。',	
	TEXT_PLEASE_SELECT: '選択して下さい。',
	TEXT_CTRL_CLICK: 'CTRL + クリックで複数の並び替え',
	TEXT_SAVE: '保存',
	TEXT_CANCEL: 'キャンセル(Cancel)',
	TEXT_PREVIEW: 'プレビュー',
	TEXT_HIDE: '隠す：',
	TEXT_QUESTION_UNSAVED_CHANGES: '他のページに行きますか？保存されてない変更は失われます。',
	
	TEXT_EDIT: '編集', 
	TEXT_COPY: 'コピー',
	TEXT_VIEW: '表示',
	TEXT_INLINE_EDIT: '編集',
	TEXT_INLINE_ADD: 'インライン追加',
	TEXT_AA_P_ADD: '追加',

//	for calendar
	TEXT_MONTH_JAN: '１月',
	TEXT_MONTH_FEB: '２月',
	TEXT_MONTH_MAR: '３月',
	TEXT_MONTH_APR: '４月',
	TEXT_MONTH_MAY: '５月',
	TEXT_MONTH_JUN: '６月',
	TEXT_MONTH_JUL: '７月',
	TEXT_MONTH_AUG: '８月',
	TEXT_MONTH_SEP: '９月',
	TEXT_MONTH_OCT: '１０月',
	TEXT_MONTH_NOV: '１１月',
	TEXT_MONTH_DEC: '１２月',
	TEXT_DAY_SU: '日',
	TEXT_DAY_MO: '月',
	TEXT_DAY_TU: '火',
	TEXT_DAY_WE: '水',
	TEXT_DAY_TH: '木',
	TEXT_DAY_FR: '金',
	TEXT_DAY_SA: '土',
	TEXT_TODAY: '今日',
	TEXT_SELECT_DATE: '日付の選択',
	TEXT_TIME: '時刻',
	TEXT_TIME_HOUR: '時',
	TEXT_TIME_MINUTE: '分',
	TEXT_TIME_SECOND: '秒',
	
//	for inline message	
	TEXT_INLINE_FIELD_REQUIRED: '必須',
	TEXT_INLINE_FIELD_ZIPCODE: 'このフィールドは有効な郵便番号である必要があります。',
	TEXT_INLINE_FIELD_EMAIL: 'このフィールドは有効なＥメールアドレスである必要があります。',
	TEXT_INLINE_FIELD_NUMBER: 'このフィールドは有効な番号である必要があります。',
	TEXT_INLINE_FIELD_CURRENCY: 'このフィールドは有効な通貨である必要があります。',
	TEXT_INLINE_FIELD_PHONE: 'このフィールドは有効な電話番号である必要があります。',
	TEXT_INLINE_FIELD_PASSWORD1: 'このフィールドは \'password\' ではいけません。',
	TEXT_INLINE_FIELD_PASSWORD2: 'このフィールドは最低４桁必要です。',
	TEXT_INLINE_FIELD_STATE: 'このフィールドは有効なアメリカの州名である必要があります。',
	TEXT_INLINE_FIELD_SSN: 'このフィールドは有効な社会保障番号(SSN)である必要があります。',
	TEXT_INLINE_FIELD_DATE: 'このフィールドは有効な日付である必要があります。',
	TEXT_INLINE_FIELD_TIME: 'このフィールドは24時間形式の時間である必要があります。',
	TEXT_INLINE_FIELD_CC: 'このフィールドは有効なクレジットカード番号である必要があります。',
	TEXT_INLINE_ERROR: 'エラー',
	TEXT_INLINE_DENY_DUPLICATES: 'フィールドの値が重複しています',
	TEXT_INLINE_USERNAME_EXISTS1: 'ユーザ名', 
	TEXT_INLINE_USERNAME_EXISTS2: 'は、既に登録されています。他のユーザ名をご利用下さい。',
	TEXT_INLINE_EMAIL_ALREADY1: 'Email', 
	TEXT_INLINE_EMAIL_ALREADY2: 'は、既に登録されています。 もしパスワードを忘れた場合は、パスワードリマインダフォームをご利用下さい。',

	//for RTE
	TEXT_VIEW_SOURCE: 'ソースを見る',
	//for tree-like menu
	TEXT_EXPAND_ALL: '全て広げる',
	TEXT_COLLAPSE_ALL: '全て折りたたむ',
	
	//for register page
	SEC_PWD_LEN: 'パスワードは最低 %% 文字必要です。',
	SEC_PWD_CASE: 'パスワードには大文字と小文字を含める必要があります。',
	SEC_PWD_DIGIT: 'パスワードには %% 桁の長さか記号を含める必要があります。',
	SEC_PWD_UNIQUE: 'パスワードには最低 %% 個のユニークな文字が必要です。',
	PASSWORDS_DONT_MATCH: 'パスワードが一致しません。',
	SUCCES_LOGGED_IN: 'ログインに成功しました。',
	
	//for pdf
	TEXT_PDF_BUILD1: 'PDF制作中',
	TEXT_PDF_BUILD2: '完了',
	TEXT_PDF_BUILD3: 'Could not create PDF',

	CLOSE_WINDOW: 'ウィンドウを閉じる',
	CLOSE: 'Close',
	RESET: 'リセット',
	
	//for search options
	CONTAINS: '次の値を含む',
	EQUALS: '次の値と等しい',
	STARTS_WITH: '次の値で始まる',
	MORE_THAN: '次の値より大きい',
	LESS_THAN: '次の値より小さい',
	BETWEEN: '次の二つの値の間',
	EMPTY: '空(Empty)',
	
	NOT_CONTAINS: '次の値を含まない',
	NOT_EQUALS: '等しくない',
	NOT_STARTS_WITH: '先頭にない',
	NOT_MORE_THAN: '次の値より大きくない',
	NOT_LESS_THAN: '次の値より少なくない',
	NOT_BETWEEN: '次の値の間に無い',
	NOT_EMPTY: '空でない',
	
	SEARCH_FOR: '検索',
	
	ERROR_MISSING_FILE_NAME: 'ファイル名が指定されていません',
	ERROR_ACCEPT_FILE_TYPES: 'ファイル型が不正です',
	ERROR_MAX_FILE_SIZE: 'ファイルの大きさが制限値(%s キロバイト)を超えました',
	ERROR_MIN_FILE_SIZE: 'ファイルの大きさは %s キロバイト以上にしてください',
	ERROR_MAX_TOTAL_FILE_SIZE: 'ファイルの合計サイズが制限(%s キロバイト)を超えました',
	ERROR_MAX_NUMBER_OF_FILES_ONE: 'アップロードできるファイルは一つだけです',
	ERROR_MAX_NUMBER_OF_FILES_MANY: 'アップロードできるのは %s 個までです',
	
	TEXT_SERVER_ERROR_OCCURRED: 'Server error occurred',
	TEXT_SEE_DETAILS: 'See details',

	ERROR_UPLOAD: 'アップロードに失敗しました',
	START_UPLOAD: 'アップロード',
	CANCEL: 'キャンセル(Cancel)',
	DELETE: '削除',
	
	UPLOAD_DRAG: 'ファイルをここにドロップ',
	
	SELECT_ALL: 'すべて選択',
	UNSELECT_ALL: 'すべて選択解除',
	
	TEXT_WR_REPORT_SAVED: 'リポートを保存しました',
	TEXT_WR_SOME_PROBLEM: '保存中にエラーがありました',
	TEXT_WR_CROSS_GROUP: 'グループ',
	TEXT_WR_HEADER: 'ヘッダー',
	TEXT_WR_CROSS_GROUP: 'グループ',
	TEXT_COUNT: '件数', 
	TEXT_MIN: '最小',
	TEXT_MAX: '最大', 
	TEXT_SUM: '合計',
	TEXT_AVG: '平均',
	TEXT_WR_TOTAL_DATA: 'テーブルデータ', 
	TEXT_PAGE_SUMMARY: 'ページの要約',
	TEXT_GLOBAL_SUMMARY: '全体の要約',
	TEXT_WR_SUMMARY: '概要',
	TEXT_FIELD: 'フィールド',
	TEXT_WR_NO_COLOR: '色指定無し',
	
	TEXT_SEARCH_SAVING: '検索を保存',
	TEXT_SEARCH_NAME: '検索名:',
	TEXT_DELETE_SEARCH_CAPTION: '保存された検索を削除',
	TEXT_DELETE_SEARCH: 'この検索を削除します。よろしいですか?',
	TEXT_YES: 'はい(Yes)',
	TEXT_NO: 'いいえ(No)',
	
	TEXT_FILTER_APPLY: '適用',
	TEXT_FILTER_CLEAR: '解除',
	TEXT_FILTER_MULTISELECT: '複数選択',
	
	// for rights page
	AA_ADD_NEW_GROUP: '新規グループの追加',
	AA_RENAMEGROUP: 'グループ名の変更',
	AA_GROUP_NEW: 'newgroup',
	AA_DELETEGROUP: '本当にこのグループを削除しますか ',
	AA_COPY_PERMISS_FROM: 'パーミッションのコピー元のグループを選択:',
	AA_CHOOSE_COLUMNS_TO_DIPLAY: '表示する列を選択',
	AA_SELECT_NONE: '選択なし',
	AA_OK: 'OK',
	
	PREPARE_PAGE_FOR_PRINTING: '印刷の準備をしています',
	
	// import page
	IMPORT_PROCESSING_RECORDS: 'レコード処理中',
	IMPORT_FAILED: 'Import Failed'
};

Runner.lang.customlabels = {
	
	prefix: 'CUSTOM_LABEL_',
	
	// custom labels
};